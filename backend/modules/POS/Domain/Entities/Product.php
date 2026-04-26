<?php

namespace Modules\POS\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Auth\Entities\User;
use Modules\POS\Domain\Enums\ProductType;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'pos_products';

    protected $fillable = [
        'name', 'amount', 'amount_type', 'description', 'image',
        'purchase_price', 'sale_price', 'supplier_id', 'category_id',
        'sub_category_id', 'ordered_count', 'is_offer', 'offer_price',
        'offer_percentage', 'type', 'production_at', 'expired_at', 'created_by', 'brand_id',
    ];

    protected $casts = [
        'is_offer'        => 'boolean',
        'purchase_price'  => 'decimal:2',
        'sale_price'      => 'decimal:2',
        'offer_price'     => 'decimal:2',
        'offer_percentage'=> 'decimal:2',
        'amount'          => 'decimal:2',
        'production_at'   => 'date',
        'expired_at'      => 'date',
    ];

    protected $appends = ['profit_percent', 'available_stock'];

    // ─── Domain business methods ──────────────────────────────────

    public function isWholesale(): bool
    {
        return (int) $this->type === ProductType::Wholesale->value;
    }

    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    public function scopeForBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function hasOffer(): bool
    {
        return (bool) $this->is_offer;
    }

    public function getEffectivePrice(): float
    {
        return $this->is_offer && $this->offer_price
            ? (float) $this->offer_price
            : (float) $this->sale_price;
    }

    public function getProfitPercentAttribute(): string
    {
        $purchase = (float) $this->purchase_price;
        $sale = (float) $this->sale_price;
        if ($purchase == 0) return '0.00%';
        return number_format((($sale - $purchase) / $purchase) * 100, 2) . '%';
    }

    public function getAvailableStockAttribute(): int
    {
        return $this->productStocks()->sum('quantity') ?? 0;
    }

    // ─── Relationships ────────────────────────────────────────────

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

    public function barcodes(): HasMany
    {
        return $this->hasMany(Barcode::class, 'product_id');
    }

    public function productStocks(): HasMany
    {
        return $this->hasMany(ProductStock::class, 'product_id');
    }

    public function offerPrices(): HasMany
    {
        return $this->hasMany(OfferPrice::class, 'product_id');
    }

    public function damaged(): HasMany
    {
        return $this->hasMany(Damaged::class, 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(\Modules\Customer\Entities\Tenant\Brand::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'pos_product_tags', 'product_id', 'tag_id');
    }
}
