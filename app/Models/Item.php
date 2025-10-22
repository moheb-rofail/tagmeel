<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Includes all fields from the migration.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'stock_quantity',
        'reorder_point',
        'unit_price',
        'selling_price',
    ];
    
    /**
     * Cast numeric fields to ensure type safety.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stock_quantity' => 'integer',
        'reorder_point' => 'integer',
        'unit_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships (To be implemented with other models)
    |--------------------------------------------------------------------------
    */

    /**
     * An Item can be part of many Sales (via a pivot table).
     */
    public function sales()
    {
        // Placeholder: Will need a many-to-many relationship with Sale model.
        // return $this->belongsToMany(Sale::class)->withPivot('quantity', 'price_at_sale');
    }

    /**
     * An Item can be part of many Purchases (via a pivot table).
     */
    public function purchases()
    {
        // Placeholder: Will need a many-to-many relationship with Purchase model.
        // return $this->belongsToMany(Purchase::class)->withPivot('quantity', 'cost_at_purchase');
    }
}
