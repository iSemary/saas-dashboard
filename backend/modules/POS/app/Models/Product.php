<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'pos_products';

    protected $fillable = [
        'name',
        'amount',
        'amount_type',
        'description',
        'image',
        'purchase_price',
        'sale_price',
        'supplier_id',
        'category_id',
        'sub_category_id',
        'ordered_count',
        'is_offer',
        'offer_price',
        'offer_percentage',
        'type',
        'production_at',
        'expired_at',
        'created_by',
    ];

    protected $casts = [
        'is_offer' => 'boolean',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'offer_percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'production_at' => 'date',
        'expired_at' => 'date',
    ];

    protected $appends = ['profit_percent'];

    public function getProfitPercentAttribute(): string
    {
        $purchase = (float) str_replace(',', '', (string) $this->purchase_price);
        $sale = (float) str_replace(',', '', (string) $this->sale_price);
        if ($purchase == 0) return '0.00%';
        $profit = (($sale - $purchase) / $purchase) * 100;
        return number_format($profit, 2) . '%';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function typeModel(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'amount_type');
    }

    public function barcode(): BelongsTo
    {
        return $this->belongsTo(Barcode::class, 'id', 'product_id');
    }

    public function barcodes(): HasMany
    {
        return $this->hasMany(Barcode::class, 'product_id');
    }

    public function productStocks(): HasMany
    {
        return $this->hasMany(ProductStock::class, 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'pos_product_tags', 'product_id', 'tag_id');
    }

    public static function getAvailableStock(int $id, ?int $branchId = null): int
    {
        $query = ProductStock::where('product_id', $id);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        return $query->sum('quantity') ?? 0;
    }
}
