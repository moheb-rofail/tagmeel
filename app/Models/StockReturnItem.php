<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReturnItem extends Model
{
    use HasFactory;

    protected $table = 'stock_return_items';

    protected $fillable = [
        'stock_return_id', // تغير ليتطابق مع اسم النموذج الجديد
        'item_id',
        'quantity',
        'unit_value',
        'sub_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_value' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    public function stockReturn()
    {
        return $this->belongsTo(StockReturn::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}