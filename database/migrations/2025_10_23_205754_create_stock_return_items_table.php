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
        // تم تغيير اسم الجدول إلى stock_return_items
        Schema::create('stock_return_items', function (Blueprint $table) {
            $table->id();
            
            // الربط بجدول stock_returns
            // تم تغيير اسم العمود إلى stock_return_id ليتطابق مع اسم الجدول
            $table->foreignId('stock_return_id')->constrained()->onDelete('cascade'); 
            
            $table->foreignId('item_id')->constrained()->onDelete('restrict'); 

            $table->integer('quantity');
            $table->decimal('unit_value', 10, 2);
            $table->decimal('sub_total', 10, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_return_items');
    }
};