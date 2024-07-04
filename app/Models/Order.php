<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<OrderStatus, Order>
     */
    public function status(): BelongsTo {
        return $this->belongsTo(OrderStatus::class);
    }
}
