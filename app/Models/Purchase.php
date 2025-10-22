<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PurchaseItem;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'invoice_number', // رقم فاتورة المورد
        'total_amount',   // المبلغ الإجمالي للفاتورة
        'status',         // حالة الدفع: 'Pending', 'Paid', 'Partial'
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the supplier that provided the goods.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the line items associated with this purchase (purchase details).
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}