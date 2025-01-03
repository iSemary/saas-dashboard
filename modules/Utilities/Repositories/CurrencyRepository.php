<?php

namespace Modules\Utilities\Repositories;

use App\Helpers\TableHelper;
use Modules\Utilities\Entities\Currency;
use Yajra\DataTables\DataTables;

class CurrencyRepository implements CurrencyInterface
{
    protected $model;

    public function __construct(Currency $currency)
    {
        $this->model = $currency;
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
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    $row,
                    'landlord.currencies.edit',
                    'landlord.currencies.destroy',
                    $this->model->pluralTitle,
                    $this->model->singleTitle,
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
        $data['status'] = isset($data['status']) && $data['status'] ? true : false;
        $data['base_currency'] = isset($data['base_currency']) && $data['base_currency'] ? true : false;

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $data['status'] = isset($data['status']) && $data['status'] ? true : false;
        $data['base_currency'] = isset($data['base_currency']) && $data['base_currency'] ? true : false;

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
