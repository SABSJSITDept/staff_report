<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'category_id',
        'question_id',
        'rating',
        'rating_given_by_id',
        'financial_year',
        'remark',
    ];

    public function category()
    {
        return $this->belongsTo(RatingCategory::class, 'category_id');
    }

    public function question()
    {
        return $this->belongsTo(RatingQuestion::class, 'question_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id'); // Assuming staff is user
    }

    public function ratingGivenBy()
    {
        return $this->belongsTo(User::class, 'rating_given_by_id');
    }
}
