<?php

namespace Modules\Auth\Repositories;

use App\Helpers\TableHelper;
use Modules\Auth\Entities\PermissionGroup;
use Yajra\DataTables\DataTables;

class PermissionGroupRepository implements PermissionGroupInterface
{
    protected $model;

    public function __construct(PermissionGroup $permissionGroup)
    {
        $this->model = $permissionGroup;
    }

    public function all()
    {
        return $this->model->with('permissions')->get();
    }

    public function datatables()
    {
        $rows = $this->model->query()->withTrashed()
            ->withCount('permissions')
            ->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->editColumn('name', function ($row) {
                return translate($row->name);
            })
            ->editColumn('guard_name', function ($row) {
                return translate($row->guard_name);
            })
            ->editColumn('description', function ($row) {
                return $row->description ?? '-';
            })
            ->addColumn('permissions_count', function ($row) {
                return $row->permissions_count ?? 0;
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.permission-groups.edit',
                    deleteRoute: 'landlord.permission-groups.destroy',
                    restoreRoute: 'landlord.permission-groups.restore',
                    type: "permission-groups",
                    titleType: "permission group",
                    showIconsOnly: false
                );
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->with('permissions')->find($id);
    }

    public function create(array $data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $permissionGroup = $this->model->create($data);

        if ($permissionGroup && !empty($permissions)) {
            $permissionGroup->permissions()->sync($permissions);
        }

        return $permissionGroup;
    }

    public function update($id, array $data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $permissionGroup = $this->model->find($id);

        if ($permissionGroup) {
            $permissionGroup->update($data);
            
            if (isset($permissions)) {
                $permissionGroup->permissions()->sync($permissions);
            }
            
            return $permissionGroup;
        }

        return null;
    }

    public function delete($id)
    {
        $row = $this->model->where("id", $id);
        if ($row->first()) {
            $row->update(['deleted_at' => now()]);
            return true;
        }
        return false;
    }

    public function restore($id)
    {
        $row = $this->model->withTrashed()->where("id", $id);
        if ($row->first()) {
            $row->update(['deleted_at' => null]);
            return true;
        }
        return false;
    }
}
