<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            
            // الربط بفاتورة الشراء
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            // الربط بالصنف
            $table->foreignId('item_id')->constrained()->onDelete('restrict'); 

            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2); // سعر الشراء الحالي لهذا الصنف
            $table->decimal('sub_total', 10, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};