<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPaginateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'trx_id'            => $this->trx_id,
            'order_code'        => $this->code,
            'total_payable'     => (string)($this->total_payable),
            'payment_status'    => __($this->payment_status),
            'date'              => Carbon::parse($this->created_at)->format('d M,Y h:m A'),
        ];
    }
}
