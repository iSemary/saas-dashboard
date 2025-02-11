<?php

namespace Modules\Email\Repositories;

use App\Helpers\TableHelper;
use Modules\Email\Entities\EmailGroup;
use Modules\Email\Entities\EmailRecipient;
use Yajra\DataTables\DataTables;

class EmailGroupRepository implements EmailGroupInterface
{
    protected $model;

    public function __construct(EmailGroup $emailGroup)
    {
        $this->model = $emailGroup;
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
            $query = $query->where('name', 'like', "%{$term}%");
        }
    
        return $query->paginate(20);
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
            ->addColumn('total_users', function ($row) {
                return $row->recipients()->count();
            })
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.email-groups.edit',
                    deleteRoute: 'landlord.email-groups.destroy',
                    restoreRoute: 'landlord.email-groups.restore',
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

    public function getRecipientsByIds($ids)
    {
        return EmailRecipient::whereIn('id', function ($query) use ($ids) {
            $query->select('email_recipient_id')
                  ->from('email_recipient_groups')
                  ->whereIn('email_group_id', $ids);
        })->get();
    }

    public function getByEmail($email)
    {
        return $this->model->whereEmail($email)->first();
    }

    public function create(array $data)
    {
        $emailRecipient = $this->model->create($data);
        return $emailRecipient;
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
