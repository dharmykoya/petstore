<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use HasUuidTrait;

    protected $fillable = ['uuid', 'title', 'slug'];
}
