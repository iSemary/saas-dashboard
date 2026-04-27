<?php

namespace Modules\Email\Repositories;

use App\Helpers\TableHelper;
use Gate;
use Modules\Email\Entities\EmailRecipient;
class EmailRecipientRepository implements EmailRecipientInterface
{
    protected $model;

    public function __construct(EmailRecipient $emailRecipient)
    {
        $this->model = $emailRecipient;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function count()
    {
        return $this->model->count();
    }

    public function getPaginated()
    {
        $query = $this->model;
    
        if (request()->has('term')) {
            $term = request()->input('term');
            $query = $query->where('email', 'like', "%{$term}%");
        }
    
        return $query->paginate(20);
    }
    

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->model->query();
        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getByEmail($email)
    {
        return $this->model->whereEmail($email)->first();
    }

    public function create(array $data)
    {
        $emailRecipient = $this->model->create($data);

        $metaKeys = $data['meta_keys'] ?? [];
        $metaValues = $data['meta_values'] ?? [];

        foreach ($metaKeys as $index => $key) {
            if (!empty($key) && isset($metaValues[$index])) {
                $emailRecipient->metas()->create([
                    'meta_key' => $key,
                    'meta_value' => $metaValues[$index]
                ]);
            }
        }

        return $emailRecipient;
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->update($data);

            $row->metas()->delete();

            $metaKeys = $data['meta_keys'] ?? [];
            $metaValues = $data['meta_values'] ?? [];

            foreach ($metaKeys as $index => $key) {
                if (!empty($key) && isset($metaValues[$index])) {
                    $row->metas()->create([
                        'meta_key' => $key,
                        'meta_value' => $metaValues[$index]
                    ]);
                }
            }

            return $row;
        }
        return null;
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
}
