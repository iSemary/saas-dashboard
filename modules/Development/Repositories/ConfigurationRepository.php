<?php

namespace Modules\Development\Repositories;

use App\Helpers\CryptHelper;
use App\Helpers\TableHelper;
use App\Services\CacheService;
use Modules\Development\Entities\Configuration;
use Yajra\DataTables\DataTables;

class ConfigurationRepository implements ConfigurationInterface
{
    protected $model;

    public function __construct(Configuration $configuration)
    {
        $this->model = $configuration;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()->withTrashed()
            ->select([
                'configurations.*',
                'types.name as type'
            ])
            ->leftJoin('types', 'configurations.type_id', '=', 'types.id')->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->filterColumn('type', function ($query, $keyword) {
                $query->whereRaw('LOWER(types.name) LIKE ?', ["%" . strtolower($keyword) . "%"]);
            })
            ->editColumn('is_encrypted', function ($row) {
                return $row->is_encrypted ? '<span><i class="fas fa-circle text-success"></i></span>' : '<span><i class="fas fa-circle text-danger"></i></span>';
            })
            ->editColumn('is_system', function ($row) {
                return $row->is_system ? '<span><i class="fas fa-circle text-success"></i></span>' : '<span><i class="fas fa-circle text-danger"></i></span>';
            })
            ->editColumn('is_visible', function ($row) {
                return $row->is_visible ? '<span><i class="fas fa-circle text-success"></i></span>' : '<span><i class="fas fa-circle text-danger"></i></span>';
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.development.configurations.edit',
                    deleteRoute: 'landlord.development.configurations.destroy',
                    restoreRoute: 'landlord.development.configurations.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: true
                );
            })
            ->rawColumns([
                'is_encrypted',
                'is_system',
                'is_visible',
                'actions'
            ])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function getByKey($key)
    {
        $cacheValue = $this->getByKeyByCache($key);
        if ($cacheValue) {
            app('log')->info(self::class . "|Cache Got configuration key: $key value: $cacheValue");
            return $cacheValue;
        }
        $databaseRow = $this->getByKeyByDatabase($key);
        if ($databaseRow) {
            CacheService::forever("configuration_{$databaseRow->configuration_key}", $databaseRow->configuration_value);
            app('log')->info(self::class . "|DB Got configuration key: $key value: $databaseRow->configuration_value");
            return $databaseRow->configuration_value;
        }
        app('log')->info(self::class . "|UNKNOWN configuration key: $key");
        return null;
    }

    public function create(array $data)
    {
        $data['is_encrypted'] = isset($data['is_encrypted']) && $data['is_encrypted'] ? true : false;
        $data['is_system'] = isset($data['is_system']) && $data['is_system'] ? true : false;
        $data['is_visible'] = isset($data['is_visible']) && $data['is_visible'] ? true : false;

        $data['configuration_value'] = $this->handleConfigurationValue($data);

        $configuration = $this->model->create($data);

        CacheService::forever("configuration_{$configuration->configuration_key}", $configuration->configuration_value);
        return $configuration;
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $data['is_encrypted'] = isset($data['is_encrypted']) && $data['is_encrypted'] ? true : false;
            $data['is_system'] = isset($data['is_system']) && $data['is_system'] ? true : false;
            $data['is_visible'] = isset($data['is_visible']) && $data['is_visible'] ? true : false;

            $data['configuration_value'] = $this->handleConfigurationValue($data, $row);

            CacheService::forget("configuration_{$row->configuration_key}");
            CacheService::forever("configuration_{$row->configuration_key}", $row->configuration_value);
            $row->update($data);
            return $row;
        }
        return null;
    }

    public function handleConfigurationValue($data, $row = null)
    {
        switch ($data['input_type']) {
            case 'string':
            case 'integer':
            case 'html':
            case 'array':
            case 'object':
            case 'date':
            case 'time':
            case 'datetime':
            case 'email':
            case 'url':
            case 'phone':
            case 'color':
            case 'range':
                return $data['configuration_value'] ?? "";
                break;

            case 'boolean':
                $data['configuration_value'] = isset($data['configuration_value']) && $data['configuration_value'] ? true : false;
                break;
            case 'file':
                // TODO upload file then
                break;
            case 'password':
                // TODO recheck this function
                $data['configuration_value'] = isset($data['configuration_value']) && !empty($data['configuration_value']) ? CryptHelper::encrypt($data['configuration_value']) : $row->configuration_value;
                break;
            default:
                return $data['configuration_value'] ?? "";
                break;
        }
    }

    public function delete($id)
    {
        $row = $this->model->find($id);
        if ($row) {
            CacheService::forget("configuration_{$row->configuration_key}");
            $row->delete();
            return true;
        }
        return false;
    }

    private function getByKeyByDatabase($key)
    {
        return $this->model->where("configuration_key", $key)->latest()->first();
    }

    private function getByKeyByCache($key)
    {
        return CacheService::get("configuration_{$key}");
    }

    public function restore($id)
    {
        $row = $this->model->withTrashed()->find($id);
        if ($row) {
            CacheService::forever("configuration_{$row->configuration_key}", $row->configuration_value);
            $row->restore();
            return true;
        }
        return false;
    }
}
