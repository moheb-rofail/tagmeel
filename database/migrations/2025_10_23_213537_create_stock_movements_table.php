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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            
            // الربط بالصنف
            $table->foreignId('item_id')->constrained()->onDelete('restrict');

            $table->date('movement_date');
            $table->string('movement_type', 3); // 'IN' (زيادة) أو 'OUT' (نقص)
            $table->integer('quantity_change'); // كمية الحركة
            
            $table->string('reference_type'); // 'Sale', 'Purchase', 'StockReturn'
            $table->unsignedBigInteger('reference_id'); // ID المرجع
            
            $table->integer('current_stock'); // المخزون بعد هذه الحركة
            
            $table->string('reason'); // سبب الحركة
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};