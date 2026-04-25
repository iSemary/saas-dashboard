<?php

namespace Modules\POS\Application\DTOs;

use Illuminate\Http\Request;

readonly class UpdateProductData
{
    public function __construct(
        public ?string $name = null,
        public ?float $amount = null,
        public ?int $amount_type = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?float $purchase_price = null,
        public ?float $sale_price = null,
        public ?int $supplier_id = null,
        public ?int $category_id = null,
        public ?int $sub_category_id = null,
        public bool $is_offer = false,
        public ?float $offer_price = null,
        public ?float $offer_percentage = null,
        public ?string $barcode_number = null,
        public ?int $tag_id = null,
        public ?string $production_at = null,
        public ?string $expired_at = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();
        $isOffer = !empty($data['is_offer']);
        $salePrice = isset($data['sale_price']) ? (float) str_replace(',', '', (string) $data['sale_price']) : null;

        return new self(
            name:            $data['name'] ?? null,
            amount:          isset($data['amount']) ? (float) $data['amount'] : null,
            amount_type:     $data['amount_type'] ?? null,
            description:     $data['description'] ?? null,
            image:           $data['image'] ?? null,
            purchase_price:  isset($data['purchase_price']) ? (float) str_replace(',', '', (string) $data['purchase_price']) : null,
            sale_price:      $salePrice,
            supplier_id:     $data['supplier_id'] ?? null,
            category_id:     $data['category_id'] ?? null,
            sub_category_id: $data['sub_category_id'] ?? null,
            is_offer:        $isOffer,
            offer_price:     ($isOffer && $salePrice) ? round($salePrice * (1 - ((float) ($data['offer_percentage'] ?? 0)) / 100), 2) : null,
            offer_percentage: $isOffer ? ($data['offer_percentage'] ?? null) : null,
            barcode_number:  $data['barcode_number'] ?? null,
            tag_id:          $data['tag_id'] ?? null,
            production_at:   $data['production_at'] ?? null,
            expired_at:      $data['expired_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'             => $this->name,
            'amount'           => $this->amount,
            'amount_type'      => $this->amount_type,
            'description'      => $this->description,
            'image'            => $this->image,
            'purchase_price'   => $this->purchase_price,
            'sale_price'       => $this->sale_price,
            'supplier_id'      => $this->supplier_id,
            'category_id'      => $this->category_id,
            'sub_category_id'  => $this->sub_category_id,
            'is_offer'         => $this->is_offer,
            'offer_price'      => $this->offer_price,
            'offer_percentage' => $this->offer_percentage,
            'production_at'    => $this->production_at,
            'expired_at'       => $this->expired_at,
        ], fn($v) => $v !== null);
    }
}
