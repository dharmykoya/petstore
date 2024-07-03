<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderStatus
 * @package App\Models
 *
 * @property string $uuid
 * @property string $title
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 */
class OrderStatus extends Model
{
    use HasFactory;
    use HasUuidTrait;

    protected $fillable = ['uuid', 'title'];

    protected $hidden = [
        'id',
    ];

    /**
     * Get the orders for the order status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
