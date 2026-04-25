<?php

namespace Modules\Customer\Repository\Tenant;

use Modules\Customer\Entities\Brand;
use Modules\Customer\Entities\Tenant\Brand as TenantBrand;
use Modules\Customer\Repository\BrandRepositoryInterface;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\TableHelper;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class BrandRepository implements BrandRepositoryInterface
{
    use TableListTrait;

    public function getAll(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->tableList(
            TenantBrand::class,
            $params,
            ['name' => 'name', 'slug' => 'slug', 'domain' => 'domain'],  // searchable columns
            ['id' => 'id', 'name' => 'name', 'slug' => 'slug', 'domain' => 'domain']  // sortable columns
        );
    }

    public function getAllLegacy(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TenantBrand::with(['creator', 'updater']);

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
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
        $rows = TenantBrand::query()->withTrashed()
            ->with(['creator', 'updater'])
            ->select([
                'brands.*',
                DB::raw('(SELECT COUNT(*) FROM branches WHERE branches.brand_id = brands.id) AS branches_count')
            ])
            ->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, 'brands', [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->addColumn('logo', function ($row) {
                if ($row->logo) {
                    return '<img src="' . asset('storage/' . $row->logo) . '" width="50" height="50" class="rounded">';
                }
                return '<img src="' . asset('assets/shared/images/icons/defaults/image.png') . '" width="50" height="50" class="rounded">';
            })
            ->addColumn('branches_count', function ($row) {
                return '<span class="badge badge-info">' . $row->branches_count . '</span>';
            })
            ->addColumn('status', function ($row) {
                $badgeClass = $row->status === 'active' ? 'success' : 'secondary';
                return '<span class="badge badge-' . $badgeClass . '">' . translate($row->status) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $actions = '';

                $actions .= '<button class="btn btn-sm btn-primary open-edit-modal me-1"
                               data-modal-link="' . route('tenant.brands.edit', $row->id) . '"
                               data-modal-title="' . translate('edit') . ' ' . translate('brand') . '">
                               <i class="fa fa-edit"></i>
                            </button>';

                if ($row->deleted_at) {
                    $actions .= '<button class="btn btn-sm btn-warning restore-btn me-1"
                                   data-route="' . route('tenant.brands.restore', $row->id) . '">
                                   <i class="fa fa-undo"></i>
                                </button>';
                } else {
                    $actions .= '<button class="btn btn-sm btn-danger delete-btn"
                                   data-route="' . route('tenant.brands.destroy', $row->id) . '">
                                   <i class="fa fa-trash"></i>
                                </button>';
                }

                return $actions;
            })
            ->rawColumns(['logo', 'branches_count', 'status', 'actions'])
            ->make(true);
    }

    public function getById(int $id): ?Brand
    {
        return TenantBrand::with(['creator', 'updater'])->find($id);
    }

    public function getBySlug(string $slug): ?Brand
    {
        return TenantBrand::with(['creator', 'updater'])->where('slug', $slug)->first();
    }

    public function create(array $data): Brand
    {
        return TenantBrand::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $brand = $this->getById($id);
        if ($brand) {
            return $brand->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $brand = $this->getById($id);
        if ($brand) {
            return $brand->delete();
        }
        return false;
    }

    public function restore(int $id): bool
    {
        $brand = Brand::withTrashed()->find($id);
        if ($brand) {
            return $brand->restore();
        }
        return false;
    }

    public function search(string $query): Collection
    {
        return TenantBrand::with(['creator', 'updater'])
            ->search($query)
            ->orderBy('name')
            ->get();
    }

    public function getDashboardStats(): array
    {
        return [
            'total' => TenantBrand::count(),
            'active' => TenantBrand::where('status', 'active')->count(),
            'inactive' => TenantBrand::where('status', 'inactive')->count(),
            'suspended' => TenantBrand::where('status', 'suspended')->count(),
            'deleted' => TenantBrand::onlyTrashed()->count(),
            'recent_30_days' => TenantBrand::where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }

    public function getByTenant(int $tenantId): Collection
    {
        return TenantBrand::where('tenant_id', $tenantId)
            ->with(['creator', 'updater'])
            ->orderBy('name')
            ->get();
    }

    public function tenantDataTables(int $tenantId)
    {
        return $this->datatables();
    }
}
