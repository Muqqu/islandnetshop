<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function seller(){
        return $this->belongsTo(User::class,'seller_id');
    }
    public function processedBy(){
        return $this->belongsTo(User::class,'processed_by');
    }
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }
}
