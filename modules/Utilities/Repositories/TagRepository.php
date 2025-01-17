<?php

namespace Modules\Utilities\Repositories;

use App\Helpers\TableHelper;
use Illuminate\Support\Facades\Gate;
use Modules\Utilities\Entities\Tag;
use Yajra\DataTables\DataTables;

class TagRepository implements TagInterface
{
    protected $model;

    public function __construct(Tag $tag)
    {
        $this->model = $tag;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables(int $id = null)
    {
        $rows = $this->model->query()
            ->where(function ($q) use ($id) {
                if ($id) {
                    $q->where('parent_id', $id);
                } else {
                    $q->where('parent_id', null);
                }
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
            });

        return DataTables::of($rows)
            ->editColumn('icon', function ($row) {
                return '<img src="' . $row->icon . '" width="50px" height="50px" alt="tag" />';
            })
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                $actionButtons = TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.tags.edit',
                    deleteRoute: 'landlord.tags.destroy',
                    restoreRoute: 'landlord.tags.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: false
                );
                // Show Button
                if (Gate::allows('update.tag_values')) {
                    $actionButtons .= '<button type="button" data-modal-title="' . translate("tag_values") . '" data-modal-link="' . route('landlord.tags.show', $row->id) . '" class="btn-info btn-sm open-details-btn">';
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
