<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;
    use HasUuidTrait;

    protected $fillable = ['uuid', 'title'];

    protected $hidden = [
        'id',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
