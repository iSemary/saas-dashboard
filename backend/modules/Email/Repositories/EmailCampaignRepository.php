<?php

namespace Modules\Email\Repositories;

use App\Helpers\TableHelper;
use Modules\Email\Entities\EmailCampaign;
use Modules\Email\Entities\EmailLog;
use Yajra\DataTables\DataTables;

class EmailCampaignRepository implements EmailCampaignInterface
{
    protected $model;

    public function __construct(EmailCampaign $emailCampaign)
    {
        $this->model = $emailCampaign;
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
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('total_users', function ($row) {
                $count = EmailLog::where('email_campaign_id', $row->id)->count();
                return '<a class="" href="' . route("landlord.emails.index") . '?campaign_id=' . $row->id . '" target="_blank">' . $count . '</a>';
            })
            ->editColumn('scheduled_at', function ($row) {
                return ($row->scheduled_at ? $row->scheduled_at : translate("instant"));
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    deleteRoute: 'landlord.email-campaigns.destroy',
                    restoreRoute: 'landlord.email-campaigns.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: false,
                    showActivityLogs: $this->model
                );
            })
            ->rawColumns(['total_users', 'scheduled_at', 'actions'])
            ->make(true);
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

    public function create(array $data)
    {
        $campaign = $this->model->create($data);
        $data['campaign'] = $campaign;

        app(EmailRepository::class)->send($data);
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
