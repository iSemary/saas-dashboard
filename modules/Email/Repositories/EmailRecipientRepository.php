<?php

namespace Modules\Email\Repositories;

use App\Helpers\TableHelper;
use Modules\Email\Entities\EmailRecipient;
use Yajra\DataTables\DataTables;

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
        return $this->model->paginate(20);
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
            ->addColumn('total_metadata', function ($row) {
                return $row->metas->count();
            })
            ->addColumn('name', function ($row) {
                return $row->metas->firstWhere('meta_key', 'name')?->meta_value ?? '';
            })
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.email-recipients.edit',
                    deleteRoute: 'landlord.email-recipients.destroy',
                    restoreRoute: 'landlord.email-recipients.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
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
