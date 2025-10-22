<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;
    
    // هذا النموذج يمثل علاقة many-to-many مع بيانات إضافية (الكمية والتكلفة)
    protected $fillable = [
        'purchase_id',
        'item_id',
        'quantity',
        'unit_cost',
        'sub_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    /**
     * Get the purchase order this detail belongs to.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the item that was purchased.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}