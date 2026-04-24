<?php

namespace Modules\Customer\Repository;

use Modules\Customer\Entities\Branch;
use Modules\Customer\Repository\BranchRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\TableHelper;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class BranchRepository implements BranchRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Branch::with(['creator', 'updater']);

        if (isset($filters['brand_id'])) {
            $query->forBrand($filters['brand_id']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        if (isset($filters['state'])) {
            $query->where('state', 'like', '%' . $filters['state'] . '%');
        }

        if (isset($filters['country'])) {
            $query->where('country', 'like', '%' . $filters['country'] . '%');
        }

        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function datatables()
    {
        $rows = Branch::query()->withTrashed()
            ->with(['creator', 'updater'])
            ->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, 'branches', [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->addColumn('brand_name', function ($row) {
                return $row->brand_name;
            })
            ->addColumn('status', function ($row) {
                $badgeClass = match($row->status) {
                    'active' => 'success',
                    'inactive' => 'secondary',
                    'suspended' => 'warning',
                    default => 'secondary'
                };
                return '<span class="badge badge-' . $badgeClass . '">' . translate($row->status) . '</span>';
            })
            ->addColumn('location', function ($row) {
                $location = [];
                if ($row->city) $location[] = $row->city;
                if ($row->state) $location[] = $row->state;
                if ($row->country) $location[] = $row->country;
                return implode(', ', $location) ?: 'N/A';
            })
            ->addColumn('manager', function ($row) {
                return $row->manager_name ?: 'N/A';
            })
            ->addColumn('working_hours', function ($row) {
                if (!$row->working_hours) {
                    return '<span class="text-muted">Not specified</span>';
                }
                
                $hours = $row->working_hours;
                $formatted = [];
                
                foreach ($hours as $day => $time) {
                    if ($time && isset($time['open']) && isset($time['close'])) {
                        $formatted[] = '<small>' . ucfirst($day) . ': ' . $time['open'] . ' - ' . $time['close'] . '</small>';
                    }
                }
                
                return empty($formatted) ? '<span class="text-muted">Not specified</span>' : implode('<br>', $formatted);
            })
            ->addColumn('actions', function ($row) {
                $actions = '';
                
                if (auth()->user()->can('update.branches')) {
                    $actions .= '<button class="btn btn-sm btn-primary open-edit-modal me-1" 
                                   data-modal-link="' . route('tenant.branches.edit', $row->id) . '"
                                   data-modal-title="' . translate('edit') . ' ' . translate('branch') . '">
                                   <i class="fa fa-edit"></i>
                                </button>';
                }
                
                if (auth()->user()->can('delete.branches')) {
                    if ($row->deleted_at) {
                        $actions .= '<button class="btn btn-sm btn-warning restore-btn me-1" 
                                       data-route="' . route('tenant.branches.restore', $row->id) . '">
                                       <i class="fa fa-undo"></i>
                                    </button>';
                    } else {
                        $actions .= '<button class="btn btn-sm btn-danger delete-btn" 
                                       data-route="' . route('tenant.branches.destroy', $row->id) . '">
                                       <i class="fa fa-trash"></i>
                                    </button>';
                    }
                }
                
                return $actions;
            })
            ->rawColumns(['status', 'working_hours', 'actions'])
            ->make(true);
    }

    public function getById(int $id): ?Branch
    {
        return Branch::with(['creator', 'updater'])->find($id);
    }

    public function getByCode(string $code): ?Branch
    {
        return Branch::with(['creator', 'updater'])->where('code', $code)->first();
    }

    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $branch = $this->getById($id);
        if ($branch) {
            return $branch->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $branch = $this->getById($id);
        if ($branch) {
            return $branch->delete();
        }
        return false;
    }

    public function restore(int $id): bool
    {
        $branch = Branch::withTrashed()->find($id);
        if ($branch) {
            return $branch->restore();
        }
        return false;
    }

    public function getByBrand(int $brandId): Collection
    {
        return Branch::with(['creator', 'updater'])
            ->forBrand($brandId)
            ->orderBy('name')
            ->get();
    }

    public function search(string $query): Collection
    {
        return Branch::with(['creator', 'updater'])
            ->search($query)
            ->orderBy('name')
            ->get();
    }

    public function getDashboardStats(): array
    {
        return [
            'total' => Branch::count(),
            'active' => Branch::where('status', 'active')->count(),
            'inactive' => Branch::where('status', 'inactive')->count(),
            'suspended' => Branch::where('status', 'suspended')->count(),
            'deleted' => Branch::onlyTrashed()->count(),
            'recent_30_days' => Branch::where('created_at', '>=', now()->subDays(30))->count(),
            'by_brand' => Branch::selectRaw('brand_id, COUNT(*) as count')
                ->groupBy('brand_id')
                ->get()
                ->mapWithKeys(function ($item) {
                    try {
                        $brand = Brand::on('landlord')->find($item->brand_id);
                        return [$brand ? $brand->name : 'Unknown Brand' => $item->count];
                    } catch (\Exception $e) {
                        return ['Unknown Brand' => $item->count];
                    }
                })
                ->toArray(),
            'by_location' => Branch::selectRaw('country, COUNT(*) as count')
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->pluck('count', 'country')
                ->toArray(),
        ];
    }

    public function getActiveBranches(): Collection
    {
        return Branch::active()
            ->orderBy('name')
            ->get();
    }

    public function getBranchesByLocation(string $city = null, string $state = null, string $country = null): Collection
    {
        $query = Branch::query();

        if ($city) {
            $query->where('city', 'like', '%' . $city . '%');
        }

        if ($state) {
            $query->where('state', 'like', '%' . $state . '%');
        }

        if ($country) {
            $query->where('country', 'like', '%' . $country . '%');
        }

        return $query->orderBy('name')->get();
    }
}
