<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'sale_date',
        'invoice_number',
        'total_amount',
        'discount_amount',
        'final_amount',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    /**
     * Get the line items associated with this sale (sale details).
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}