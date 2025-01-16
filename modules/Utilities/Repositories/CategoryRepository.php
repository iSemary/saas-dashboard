<?php

namespace Modules\Utilities\Repositories;

use App\Helpers\TableHelper;
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
        $rows = $this->model->query()
            ->select([
                'categories.*',
                DB::raw('(SELECT name FROM categories AS parent WHERE parent.id = categories.parent_id) AS parent_name')
            ])->whereNull("deleted_at")->where(
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
            ->editColumn('icon', function ($row) {
                return '<img src="' . $row->icon . '" width="50px" height="50px" alt="category" />';
            })
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    $row,
                    'landlord.categories.edit',
                    'landlord.categories.destroy',
                    $this->model->pluralTitle,
                    $this->model->singleTitle,
                    true
                );
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
        if (isset($data['icon']) && $data['icon'] instanceof \Illuminate\Http\UploadedFile) {
            $model = new $this->model;
            $media = $model->upload($data['icon']);
            $data['icon'] = $media->id;
        }
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
