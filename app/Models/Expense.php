<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'amount',
        'expense_date',
        'category', // e.g., 'Office Supplies', 'Utilities', 'Travel'
        'notes',
        'receipt_path', // Optional: Path to a stored receipt image/file
        // 'user_id', // Uncomment if you want to track which user created the expense
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user that recorded the expense (if applicable).
     */
    /*
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    */
}