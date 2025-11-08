<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'initial_balance', // الرصيد الافتتاحي (مدين/دائن)
        'current_balance', // الرصيد الحالي (محدث آلياً)
        'balance_type',    // 'Debit' (مدين/عليه) أو 'Credit' (دائن/له)
        'notes',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    /**
     * العلاقة مع المبيعات التي قام بها هذا العميل (في حال دمجها مع Sales).
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}