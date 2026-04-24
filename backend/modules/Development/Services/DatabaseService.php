<?php

namespace Modules\Development\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Development\Entities\DatabaseFlow;

class DatabaseService
{
    public function getDatabaseStructure(string $connection = 'landlord'): array
    {
        $tables = $this->getTables($connection);
        $structure = [];

        foreach ($tables as $table) {
            $columns = $this->getTableColumns($connection, $table);
            $relations = $this->getTableRelations($connection, $table);
            $flow = $this->getTableFlow($connection, $table);

            $structure[] = [
                'name' => $table,
                'columns' => $columns,
                'relations' => $relations,
                'design' => $flow ?? null,
            ];
        }

        return $structure;
    }

    public function syncFlow(array $nodes): void
    {
        foreach ($nodes as $node) {
            DatabaseFlow::updateOrCreate(
                ['table' => $node['table'], 'connection' => $node['connection']],
                ['position' => $node['position'], 'color' => $node['color']]
            );
        }
    }

    protected function getTableFlow(string $connection, string $table)
    {
        $flow = DatabaseFlow::where('connection', $connection)->where("table", $table)->first();
        return $flow ?: [];
    }

    protected function getTables(string $connection): array
    {
        $tables = DB::connection($connection)->select('SHOW TABLES');
        return array_map(function ($table) {
            return reset($table);
        }, $tables);
    }

    protected function getTableColumns(string $connection, string $table): array
    {
        $columns = [];
        $columnList = Schema::connection($connection)->getColumnListing($table);
        $detailedColumns = DB::connection($connection)->select("SHOW FULL COLUMNS FROM {$table}");
        $totalRows = DB::connection($connection)->table($table)->count();

        foreach ($columnList as $column) {
            $columnInfo = collect($detailedColumns)->where('Field', $column)->first();
            $columns[] = [
                'title' => $column,
                'data_type' => $columnInfo->Type,
                'nullable' => $columnInfo->Null === 'YES',
                'total_rows' => $totalRows,
                'collation' => $columnInfo->Collation,
                'default' => $columnInfo->Default,
            ];
        }

        return $columns;
    }

    protected function getTableRelations(string $connection, string $table): array
    {
        $database = config("database.connections.{$connection}.database");

        $relations = DB::connection($connection)->select("
            SELECT 
                COLUMN_NAME as column_name,
                REFERENCED_TABLE_NAME as foreign_table,
                REFERENCED_COLUMN_NAME as foreign_column
            FROM
                information_schema.KEY_COLUMN_USAGE
            WHERE
                REFERENCED_TABLE_SCHEMA = :database
                AND TABLE_NAME = :table
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ", ['database' => $database, 'table' => $table]);

        $formattedRelations = [];
        foreach ($relations as $relation) {
            $formattedRelations[] = [
                'column' => $relation->column_name,
                'references' => [
                    'table' => $relation->foreign_table,
                    'column' => $relation->foreign_column
                ]
            ];
        }

        return $formattedRelations;
    }
}
