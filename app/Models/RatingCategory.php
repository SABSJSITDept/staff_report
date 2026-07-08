<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function questions()
    {
        return $this->hasMany(RatingQuestion::class, 'category_id');
    }
}
