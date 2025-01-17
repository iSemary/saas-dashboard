<?php

namespace Modules\FileManager\Repositories;

use App\Helpers\FileHelper;
use App\Helpers\TableHelper;
use Modules\FileManager\Entities\File;
use Yajra\DataTables\DataTables;

class FileRepository implements FileInterface
{
    protected $model;

    public function __construct(File $file)
    {
        $this->model = $file;
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
            ->filterColumn('folder', function ($query, $keyword) {
                $query->whereHas('folder', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
            })
            ->editColumn('folder', function ($row) {
                return $row->folder->name;
            })
            ->editColumn('size', function ($row) {
                return FileHelper::returnSizeString($row->size);
            })
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->editColumn('access_level', function ($row) {
                if ($row->access_level == 'private') {
                    return '<i class="fa fa-lock text-danger" title="'.$row->access_level.'"></i>';
                }
                return '<i class="fa fa-unlock text-success" title="'.$row->access_level.'"></i>';
            })
            ->editColumn('host', function ($row) {
                if ($row->host == 'aws') {
                    return '<i class="fab fa-aws text-orange" title="'.$row->host.'"></i>';
                }
                return '<i class="fas fa-warehouse text-primary" title="'.$row->host.'"></i>';
                
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row:$row,
                    editRoute:null,
                    deleteRoute:'landlord.development.files.destroy',
                    type:$this->model->pluralTitle,
                    titleType:$this->model->singleTitle,
                    showIconsOnly:true
                );
            })
            ->rawColumns(['folder', 'host', 'access_level', 'actions'])
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
