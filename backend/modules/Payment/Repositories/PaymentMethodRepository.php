<?php

namespace Modules\Payment\Repositories;

use App\Helpers\TableHelper;
use Modules\Payment\Entities\PaymentMethod;
use Yajra\DataTables\DataTables;

class PaymentMethodRepository implements PaymentMethodInterface
{
    protected $model;

    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->model = $paymentMethod;
    }

    public function all()
    {
        return $this->model->with(['currencies', 'fees', 'limits'])->get();
    }

    public function datatables()
    {
        $rows = $this->model->query()->withTrashed()->where(
            function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
            }
        )->with(['currencies']);

        return DataTables::of($rows)
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.payment-methods.edit',
                    deleteRoute: 'landlord.payment-methods.destroy',
                    restoreRoute: 'landlord.payment-methods.restore',
                    type: $row->pluralTitle,
                    titleType: $row->singleTitle,
                    showIconsOnly: false
                );
            })
            ->addColumn('status', function ($row) {
                $statusColors = [
                    'active' => 'success',
                    'inactive' => 'secondary',
                    'maintenance' => 'warning'
                ];
                $color = $statusColors[$row->status] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('supported_currencies', function ($row) {
                $currencies = $row->supported_currencies ?? [];
                if (count($currencies) > 3) {
                    $display = implode(', ', array_slice($currencies, 0, 3)) . ' +' . (count($currencies) - 3);
                } else {
                    $display = implode(', ', $currencies);
                }
                return '<span class="text-muted">' . $display . '</span>';
            })
            ->addColumn('is_global', function ($row) {
                return $row->is_global 
                    ? '<span class="badge bg-success">Global</span>' 
                    : '<span class="badge bg-secondary">Regional</span>';
            })
            ->rawColumns(['actions', 'status', 'supported_currencies', 'is_global'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->with(['currencies', 'fees', 'limits', 'configurations'])->find($id);
    }

    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? 'active';
        $data['is_global'] = isset($data['is_global']) && $data['is_global'] ? true : false;
        $data['priority'] = $data['priority'] ?? 0;
        $data['success_rate'] = 0;
        $data['average_processing_time'] = 0;

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $paymentMethod = $this->find($id);
        
        if (!$paymentMethod) {
            return false;
        }

        $data['is_global'] = isset($data['is_global']) && $data['is_global'] ? true : false;

        return $paymentMethod->update($data);
    }

    public function delete($id)
    {
        return $this->model->find($id)?->delete();
    }

    public function restore($id)
    {
        return $this->model->withTrashed()->find($id)?->restore();
    }

    public function getActive()
    {
        return $this->model->active()->orderBy('priority', 'desc')->get();
    }

    public function getByProcessor($processorType)
    {
        return $this->model->byProcessor($processorType)->get();
    }

    public function getAvailableForCountry($countryCode)
    {
        return $this->model->active()
                          ->where(function ($query) use ($countryCode) {
                              $query->where('is_global', true)
                                    ->orWhereJsonContains('country_codes', $countryCode);
                          })
                          ->get();
    }

    public function getAvailableForCurrency($currencyCode)
    {
        return $this->model->active()
                          ->whereJsonContains('supported_currencies', $currencyCode)
                          ->get();
    }
}
