<?php

namespace Modules\Tenant\Repositories;

use App\Helpers\TableHelper;
use Modules\Auth\Entities\User;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class SystemUserRepository implements SystemUserInterface
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()->where(
            function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
            }
        );

        return DataTables::of($rows)
            ->addColumn('role', function ($row) {
                return $row->role()?->name ? translate($row->role()?->name) : translate('unset');
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    $row,
                    'landlord.system-users.edit',
                    'landlord.system-users.destroy',
                    "system_users",
                    "system_user",
                    true
                );
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($id, array $data)
    {
        $user = $this->model->find($id);

        if (!$user) {
            return null;
        }

        $user->update($data);

        if (isset($data['permissions'])) {
            $user->permissions()->sync($data['permissions']);
        }

        return $user;
    }

    public function create(array $data)
    {
        $user = $this->model->create($data);

        $role = Role::where("name", "landlord")->first();

        $user->assignRole($role->id);

        $user->permissions()->sync($data['permissions']);

        return $user;
    }

    public function delete($id)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->delete();
            return true;
        }
        return false;
    }
}
