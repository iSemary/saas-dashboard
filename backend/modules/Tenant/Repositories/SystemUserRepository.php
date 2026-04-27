<?php

namespace Modules\Tenant\Repositories;

use App\Helpers\TableHelper;
use Illuminate\Support\Facades\Gate;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;
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

        if (isset($data['role_id']) && is_array($data['role_id'])) {
            $roles = Role::whereIn('id', $data['role_id'])->get();
            $user->roles()->sync($roles->pluck('id')->toArray());
        }

        if (isset($data['permissions'])) {
            $user->permissions()->sync($data['permissions']);
        }

        return $user;
    }

    public function create(array $data)
    {
        $user = $this->model->create($data);

        if (isset($data['role_id']) && is_array($data['role_id'])) {
            $roles = Role::whereIn('id', $data['role_id'])->get();
            $user->roles()->sync($roles->pluck('id')->toArray());
        } else {
            $role = Role::where("name", "landlord")->first();
            $user->assignRole($role->id);
        }
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

    public function restore($id)
    {
        $row = $this->model->withTrashed()->find($id);
        if ($row) {
            $row->restore();
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
