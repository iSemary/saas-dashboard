<?php

namespace Modules\FileManager\Repositories;

use App\Helpers\FileHelper;
use App\Helpers\TableHelper;
use Modules\FileManager\Entities\File;
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
