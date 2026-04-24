<?php

namespace Modules\Utilities\Repositories;

use App\Helpers\TableHelper;
use App\Helpers\TranslateHelper;
use Illuminate\Support\Facades\DB;
use Modules\Utilities\Entities\Category;
use Yajra\DataTables\DataTables;

class CategoryRepository implements CategoryInterface
{
    protected $model;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()->withTrashed()
            ->select([
                'categories.*',
                DB::raw('(SELECT name FROM categories AS parent WHERE parent.id = categories.parent_id) AS parent_name')
            ])
            ->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->filterColumn('parent_name', function ($query, $keyword) {
                $query->whereRaw('LOWER(categories.name) LIKE ?', ["%{$keyword}%"]);
            })
            ->editColumn('name', function($row) {
                return TranslateHelper::returnTranslatableEditor($row, 'name');
            })
            ->editColumn('description', function($row) {
                return TranslateHelper::returnTranslatableEditor($row, 'description');
            })
            ->editColumn('icon', function ($row) {
                return '<img src="' . $row->icon . '" width="50px" height="50px" class="img-thumbnail" alt="category" />';
            })
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.categories.edit',
                    deleteRoute: 'landlord.categories.destroy',
                    restoreRoute: 'landlord.categories.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: true
                );
            })
            ->rawColumns(['name', 'description', 'icon', 'actions'])
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
