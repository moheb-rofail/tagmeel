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
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('customer_id')->constrained()->onDelete('restrict'); 
            
            $table->date('payment_date');
            $table->decimal('amount', 10, 2); // المبلغ المسدد
            $table->string('payment_method')->nullable();
            
            $table->decimal('previous_balance', 10, 2); // رصيد العميل قبل السداد
            $table->decimal('new_balance', 10, 2);      // رصيد العميل بعد السداد
            $table->string('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};