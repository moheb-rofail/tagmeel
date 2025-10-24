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
        // تم تغيير اسم الجدول إلى stock_returns
        Schema::create('stock_returns', function (Blueprint $table) { 
            $table->id();
            
            $table->string('return_type', 10); // 'sale' or 'purchase'
            $table->unsignedBigInteger('reference_id')->nullable(); 
            
            $table->date('return_date');
            $table->string('customer_supplier_name')->nullable();
            
            $table->decimal('total_amount', 10, 2); 
            $table->text('reason')->nullable();
            $table->string('status')->default('Processed'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_returns');
    }
};