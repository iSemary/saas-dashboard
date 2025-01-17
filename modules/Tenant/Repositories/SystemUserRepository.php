<?php

namespace Modules\Tenant\Repositories;

use App\Helpers\TableHelper;
use Illuminate\Support\Facades\Gate;
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
        $rows = $this->model->query()->withTrashed()->where(
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
                $actionButtons = TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.system-users.edit',
                    deleteRoute: 'landlord.system-users.destroy',
                    restoreRoute: 'landlord.system-users.restore',
                    type: "system_users",
                    titleType: "system_user",
                    showIconsOnly: true
                );

                if (Gate::allows('read.activity_logs')) {
                    $actionButtons .= '<button type="button" title="' . translate("activity_logs") . '" data-modal-title="' . translate("activity_logs") . '" data-modal-link="' . route('landlord.attempts.index', $row->id) . '" class="btn-blue mx-1 btn-sm open-details-btn">';
                    $actionButtons .=  '<i class="fas fa-user-clock"></i>';
                    $actionButtons .= '</button>';
                }

                if (Gate::allows('read.login_attempts')) {
                    $actionButtons .= '<button type="button" title="' . translate("login_attempts") . '" data-modal-title="' . translate("login_attempts") . '" data-modal-link="' . route('landlord.attempts.index', $row->id) . '" class="btn-teal ms-1 btn-sm open-details-btn">';
                    $actionButtons .=  '<i class="fas fa-fingerprint"></i>';
                    $actionButtons .= '</button>';
                }

                return $actionButtons;
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

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
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

    public function checkEmail($email, $id = null)
    {
        $query = $this->model->where('email', $email);
        if ($id) {
            $query->where('id', '!=', $id);
        }
        return $query->exists();
    }
}
