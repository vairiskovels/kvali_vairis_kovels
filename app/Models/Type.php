<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Expense;
use App\Models\Color;
use App\Models\ResetToken;

class Type extends Model
{
    use HasFactory;
    public function expense() {
        return $this->hasMany(Expense::class);
    }
    public function color() {
        return $this->hasOne(Color::class);
    }
    public function budget() {
        return $this->hasMany(ResetToken::class);
    }
}
