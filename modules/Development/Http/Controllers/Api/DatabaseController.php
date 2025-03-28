<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Modules\Development\Entities\DatabaseFlow;

class DatabaseController extends ApiController
{
    public function index()
    {
        $databases = [
            'landlord' => $this->getDatabaseStructure('landlord')
        ];

        return $this->return(200, 'Database fetched successfully', ['databases' => $databases]);
    }

    public function syncFlow(Request $request)
    {
        $validated = $request->validate([
            'nodes' => 'required|array',
            'nodes.*.connection' => 'required|string',
            'nodes.*.table' => 'required|string',
            'nodes.*.position' => 'required|array',
            'nodes.*.position.x' => 'required|numeric',
            'nodes.*.position.y' => 'required|numeric',
            'nodes.*.color' => 'required|string',
        ]);

        foreach ($validated['nodes'] as $node) {
            DatabaseFlow::updateOrCreate(
                ['table' => $node['table'], 'connection' => $node['connection']],
                ['position' => $node['position'], 'color' => $node['color']]
            );
        }

        return response()->json(['success' => true, 'message' => 'Flow saved successfully.']);
    }
    
    protected function getDatabaseStructure($connection)
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

    protected function getTableFlow($connection, $table)
    {
        $flow = DatabaseFlow::where('connection', $connection)->where("table", $table)->first();

        if (!$flow) {
            $flow = [];
        }
        return $flow;
    }

    protected function getTables($connection)
    {
        $database = config("database.connections.{$connection}.database");
        $tables = DB::connection($connection)->select('SHOW TABLES');

        return array_map(function ($table) {
            return reset($table); // Get the first value of the object
        }, $tables);
    }

    protected function getTableColumns($connection, $table)
    {
        $columns = [];
        $columnList = Schema::connection($connection)->getColumnListing($table);

        // Get detailed column information
        $detailedColumns = DB::connection($connection)
            ->select("SHOW FULL COLUMNS FROM {$table}");

        // Get total rows for the table
        $totalRows = DB::connection($connection)
            ->table($table)
            ->count();

        foreach ($columnList as $column) {
            $columnInfo = collect($detailedColumns)
                ->where('Field', $column)
                ->first();

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

    protected function getTableRelations($connection, $table)
    {
        $database = config("database.connections.{$connection}.database");

        $relations = DB::connection($connection)
            ->select("
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
            ", [
                'database' => $database,
                'table' => $table
            ]);

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
