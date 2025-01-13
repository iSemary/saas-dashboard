<?php

namespace Modules\Utilities\Repositories;

use App\Helpers\TableHelper;
use Illuminate\Support\Facades\Gate;
use Modules\Utilities\Entities\TagValue;
use Yajra\DataTables\DataTables;

class TagValueRepository implements TagValueInterface
{
    protected $model;

    public function __construct(TagValue $tagValue)
    {
        $this->model = $tagValue;
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
            ->editColumn('icon', function ($row) {
                return '<img src="' . $row->icon . '" width="50px" height="50px" alt="tag" />';
            })
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                $actionButtons = TableHelper::actionButtons(
                    $row,
                    'landlord.tags.edit',
                    'landlord.tags.destroy',
                    $this->model->pluralTitle,
                    $this->model->singleTitle,
                );
                // Show Button
                if (Gate::allows('update.' . $this->model->pluralTitle)) {
                    $actionButtons .= '<button type="button" data-modal-title="'.translate("tag_values").'" data-modal-link="' . route('landlord.tags.show', $row->id) . '" class="btn-info btn-sm open-details-btn">';
                    $actionButtons .=  '<i class="fas fa-info-circle"></i> ' . translate('values');
                    $actionButtons .= '</button>';
                }
                return $actionButtons;
            })
            ->rawColumns(['icon', 'actions'])
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
}
