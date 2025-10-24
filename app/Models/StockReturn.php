<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReturn extends Model
{
    use HasFactory;

    // ملاحظة: جدول قاعدة البيانات سيسمى 'stock_returns' (صيغة الجمع التلقائية)
    protected $table = 'stock_returns';

    protected $fillable = [
        'return_type',      // 'sale' or 'purchase'
        'reference_id',     // ID of the original Sale or Purchase
        'return_date',
        'customer_supplier_name', 
        'total_amount',
        'reason',
        'status',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(StockReturnItem::class);
    }
    
    // ... (يمكن إضافة علاقة المرجع هنا)
}