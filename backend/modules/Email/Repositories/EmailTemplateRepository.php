<?php

namespace Modules\Email\Repositories;

use App\Helpers\TableHelper;
use Modules\Email\Entities\EmailTemplate;
use Yajra\DataTables\DataTables;

class EmailTemplateRepository implements EmailTemplateInterface
{
    protected $model;

    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->model = $emailTemplate;
    }

    public function all(array $conditions = [])
    {
        $query = $this->model;

        if (!empty($conditions)) {
            $query = $query->where($conditions);
        }

        return $query->get();
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
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.email-templates.edit',
                    deleteRoute: 'landlord.email-templates.destroy',
                    restoreRoute: 'landlord.email-templates.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: false,
                    showActivityLogs: $this->model
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
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->update($data);
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
