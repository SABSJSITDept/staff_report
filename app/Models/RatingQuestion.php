<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'question'];

    public function category()
    {
        return $this->belongsTo(RatingCategory::class, 'category_id');
    }
}
