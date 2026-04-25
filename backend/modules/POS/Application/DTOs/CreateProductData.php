<?php

namespace Modules\POS\Application\DTOs;

use Illuminate\Http\Request;

readonly class CreateProductData
{
    public function __construct(
        public string $name,
        public float $amount,
        public ?int $amount_type,
        public ?string $description,
        public ?string $image,
        public float $purchase_price,
        public float $sale_price,
        public ?int $supplier_id,
        public ?int $category_id,
        public ?int $sub_category_id,
        public bool $is_offer,
        public ?float $offer_price,
        public ?float $offer_percentage,
        public int $type,
        public ?string $production_at,
        public ?string $expired_at,
        public ?string $barcode_number,
        public ?int $tag_id,
        public ?float $wholesale_purchase_price,
        public ?float $wholesale_sale_price,
        public ?int $wholesale_quantity,
        public ?string $wholesale_barcode,
        public bool $wholesale_product,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();
        $isOffer = !empty($data['is_offer']);
        $salePrice = (float) str_replace(',', '', (string) ($data['sale_price'] ?? 0));

        return new self(
            name:                    $data['name'],
            amount:                  (float) ($data['amount'] ?? 0),
            amount_type:             $data['amount_type'] ?? null,
            description:             $data['description'] ?? null,
            image:                   $data['image'] ?? null,
            purchase_price:          (float) str_replace(',', '', (string) ($data['purchase_price'] ?? 0)),
            sale_price:              $salePrice,
            supplier_id:             $data['supplier_id'] ?? null,
            category_id:             $data['category_id'] ?? null,
            sub_category_id:         $data['sub_category_id'] ?? null,
            is_offer:                $isOffer,
            offer_price:             $isOffer ? round($salePrice * (1 - ((float) ($data['offer_percentage'] ?? 0)) / 100), 2) : null,
            offer_percentage:        $isOffer ? (float) ($data['offer_percentage'] ?? 0) : null,
            type:                    (int) ($data['type'] ?? 1),
            production_at:           $data['production_at'] ?? null,
            expired_at:              $data['expired_at'] ?? null,
            barcode_number:          $data['barcode_number'] ?? null,
            tag_id:                  $data['tag_id'] ?? null,
            wholesale_purchase_price: isset($data['wholesale_purchase_price']) ? (float) $data['wholesale_purchase_price'] : null,
            wholesale_sale_price:    isset($data['wholesale_sale_price']) ? (float) $data['wholesale_sale_price'] : null,
            wholesale_quantity:      isset($data['wholesale_quantity']) ? (int) $data['wholesale_quantity'] : null,
            wholesale_barcode:       $data['wholesale_barcode'] ?? null,
            wholesale_product:       !empty($data['wholesale_product']),
        );
    }

    public function toArray(): array
    {
        return [
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
            'type'             => $this->type,
            'production_at'    => $this->production_at,
            'expired_at'       => $this->expired_at,
        ];
    }
}
