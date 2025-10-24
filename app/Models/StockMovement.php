<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    // بما أن هذا النموذج هو سجل لتغييرات المخزون، يجب أن يتم إنشاؤه
    // تلقائيًا عبر المتحكمات الأخرى (Sale, Purchase, StockReturn) وليس مباشرة.
    protected $fillable = [
        'item_id',
        'movement_date',
        'movement_type',    // 'IN' (زيادة) أو 'OUT' (نقص)
        'quantity_change',  // الكمية التي تغيرت (دائماً قيمة موجبة)
        'reference_type',   // 'Sale', 'Purchase', 'StockReturn'
        'reference_id',     // ID الفاتورة الأصلية
        'reason',           // سبب الحركة (مثل 'Sale', 'Purchase', 'SaleReturn')
        'current_stock',    // المخزون بعد هذه الحركة
    ];

    protected $casts = [
        'movement_date' => 'date',
        'quantity_change' => 'integer',
        'current_stock' => 'integer',
    ];

    /**
     * Get the item associated with the movement.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the reference transaction (polymorphic relationship - يحتاج لمتابعة في المستقبل).
     * حالياً نكتفي بالربط العادي reference_type و reference_id.
     */
}