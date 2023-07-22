<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'order_code'        => $this->code,
            'customer_name'     => $this->shipping_address['name'],
            'customer_email'    => $this->shipping_address['email'],
            'customer_phone'    => $this->shipping_address['phone_no'],
            'address'           => nullCheck($this->shipping_address['address']),
            'country'           => nullCheck($this->shipping_address['country']),
            'state'             => nullCheck($this->shipping_address['state']),
            'city'              => $this->shipping_address['city'],
            'collected_amount'  => get_price($this->collected_amount),
            'payment_status'    => __($this->payment_status),
            'date'              => Carbon::parse($this->created_at)->format('d-m-Y'),
        ];

    }
}
