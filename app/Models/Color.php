<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Type;

class Color extends Model
{
    use HasFactory;
    public function type() {
        return $this->hasOne(Type::class);
    }
}
