<?php

namespace Modules\Auth\Repositories;

use App\Helpers\TableHelper;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Entities\Role;
use Yajra\DataTables\DataTables;

class RoleRepository implements RoleInterface
{
    protected $model;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows =  $this->model->query()->withTrashed()
            ->select([
                'roles.*',
                DB::raw('COUNT(role_has_permissions.permission_id) as total_permissions')
            ])
            ->leftJoin('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->groupBy('roles.id')->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->editColumn('name', function ($row) {
                return translate($row->name);
            })->editColumn('guard_name', function ($row) {
                return translate($row->guard_name);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.roles.edit',
                    deleteRoute: 'landlord.roles.destroy',
                    restoreRoute: 'landlord.roles.restore',
                    type: "roles",
                    titleType: "role",
                    showIconsOnly: false
                );
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = $this->model->create($data);

        if ($role && !empty($permissions)) {
            $role->permissions()->sync($permissions);
        }

        return $role;
    }

    public function update($id, array $data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = $this->model->find($id);

        if ($role) {
            $role->update($data);
            $role->permissions()->sync($permissions);
            return $role;
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
