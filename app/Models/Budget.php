<?php

namespace App\Models;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'max_amount',
        'frequency',
        'end_date',
    ];

    // A budget belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A budget belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
