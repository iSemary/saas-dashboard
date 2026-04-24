<?php

namespace Modules\Customer\Repository;

use Modules\Customer\Entities\Brand;
use Modules\Customer\Repository\BrandRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\TableHelper;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BrandRepository implements BrandRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Brand::on('landlord')->with(['tenant', 'creator', 'updater']);

        if (isset($filters['tenant_id'])) {
            $query->forTenant($filters['tenant_id']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
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
        $rows = Brand::on('landlord')->query()->withTrashed()
            ->with(['tenant', 'creator', 'updater'])
            ->select([
                'brands.*',
                DB::raw('(SELECT name FROM tenants WHERE tenants.id = brands.tenant_id) AS tenant_name')
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
            ->addColumn('tenant_name', function ($row) {
                return $row->tenant ? $row->tenant->name : 'N/A';
            })
            ->addColumn('status', function ($row) {
                $badgeClass = $row->status === 'active' ? 'success' : 'secondary';
                return '<span class="badge badge-' . $badgeClass . '">' . translate($row->status) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $actions = '';

                $actions .= '<button class="btn btn-sm btn-primary open-edit-modal me-1"
                               data-modal-link="' . route('landlord.brands.edit', $row->id) . '"
                               data-modal-title="' . translate('edit') . ' ' . translate('brand') . '">
                               <i class="fa fa-edit"></i>
                            </button>';

                if ($row->deleted_at) {
                    $actions .= '<button class="btn btn-sm btn-warning restore-btn me-1"
                                   data-route="' . route('landlord.brands.restore', $row->id) . '">
                                   <i class="fa fa-undo"></i>
                                </button>';
                } else {
                    $actions .= '<button class="btn btn-sm btn-danger delete-btn"
                                   data-route="' . route('landlord.brands.destroy', $row->id) . '">
                                   <i class="fa fa-trash"></i>
                                </button>';
                }

                return $actions;
            })
            ->rawColumns(['logo', 'status', 'actions'])
            ->make(true);
    }

    public function tenantDataTables($tenantId)
    {
        $rows = Brand::on('landlord')->query()
            ->with(['creator', 'updater'])
            ->select([
                'brands.*',
                DB::raw('(SELECT COUNT(*) FROM branches WHERE branches.brand_id = brands.id) AS branches_count')
            ])
            ->where('tenant_id', $tenantId)
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

                // Only show view action for tenants (read-only)
                $actions .= '<a href="' . route('tenant.brands.show', $row->id) . '" class="btn btn-sm btn-primary me-1">
                               <i class="fa fa-eye"></i>
                            </a>';

                return $actions;
            })
            ->rawColumns(['logo', 'branches_count', 'status', 'actions'])
            ->make(true);
    }

    public function getById(int $id): ?Brand
    {
        return Brand::with(['tenant', 'creator', 'updater'])->find($id);
    }

    public function getBySlug(string $slug): ?Brand
    {
        return Brand::with(['tenant', 'creator', 'updater'])->where('slug', $slug)->first();
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
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
        $brand = Brand::on('landlord')->withTrashed()->find($id);
        if ($brand) {
            return $brand->restore();
        }
        return false;
    }

    public function getByTenant(int $tenantId): Collection
    {
        return Brand::on('landlord')
            ->with(['creator', 'updater'])
            ->forTenant($tenantId)
            ->orderBy('name')
            ->get();
    }

    public function search(string $query): Collection
    {
        return Brand::on('landlord')->with(['tenant', 'creator', 'updater'])
            ->search($query)
            ->orderBy('name')
            ->get();
    }

    public function getDashboardStats(): array
    {
        return [
            'total' => Brand::on('landlord')->count(),
            'active' => Brand::on('landlord')->count(),
            'deleted' => Brand::on('landlord')->onlyTrashed()->count(),
            'recent_30_days' => Brand::on('landlord')->where('created_at', '>=', now()->subDays(30))->count(),
            'by_tenant' => Brand::on('landlord')->selectRaw('tenant_id, COUNT(*) as count')
                ->groupBy('tenant_id')
                ->with('tenant:id,name')
                ->get()
                ->pluck('count', 'tenant.name')
                ->toArray(),
        ];
    }
}
