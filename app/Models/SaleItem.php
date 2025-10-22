<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'item_id',
        'quantity',
        'unit_price', // سعر البيع في وقت العملية
        'sub_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    /**
     * Get the sale order this detail belongs to.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the item that was sold.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}