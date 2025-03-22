<?php

namespace App\Models;

use App\Models\User;
use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
    ];

    // A category belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A category has many transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // A category has many budgets
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}
