<?php

namespace App\Http\Resources\AdminResource;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StockPaginateResource extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $tax =0;
                if (settingHelper('vat_and_tax_type') == 'product_base'):
                    $price = $data->product->has_variant == 1 ? $data->price : $data->product->price;
                    foreach ($data->product->vatTaxes($data->product) as $product_tax) :
                        $tax += ($price * $product_tax->percentage) / 100;
                    endforeach;
                endif;
                return [
                    'id'                                    => $data->id,
                    'product_id'                            => $data->product_id,
                    'seller_id'                             => $data->product->user_id,
                    'current_stock'                         => (int)$data->current_stock,
                    'stock_image'                           => $data->image_190x230,
                    'stock_price'                           => (double)$data->price,
                    'name'                                  => $data->name,
                    'product_name'                          => @$data->product->product_name,
                    'stock_discount_price'                  => @$data->discount_percentage,
                    'special_discount_type'                 => nullCheck(@$data->product->special_discount_type),
                    'special_discount'                      => @$data->product->special_discount_check,
                    'discount_price'                        => @$data->product->discount_percentage,
                    'has_variant'                           => (double)@$data->product->has_variant,
                    'product_stock'                         => (int)@$data->product->current_stock,
                    'product_price'                         => (int)@$data->product->price,
                    'product_image'                         => $data->product->image_190x230,
                    'tax'                                   => $tax,
                    'barcode'                               => $data->product->barcode,
                    'min_order_quantity'                    => $data->product->minimum_order_quantity,
                ];
            }),

            'total'         => $this->total(),
            'count'         => $this->count(),
            'per_page'      => $this->perPage(),
            'current_page'  => $this->currentPage(),
            'total_pages'   => $this->lastPage(),
            'last_page'     => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'has_more_data' => $this->hasMorePages(),

        ];
    }
}
