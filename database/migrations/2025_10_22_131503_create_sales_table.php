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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->date('sale_date');
            $table->string('invoice_number')->unique()->nullable();

            // المبالغ
            $table->decimal('total_amount', 10, 2);  // المبلغ الإجمالي قبل الخصم
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_amount', 10, 2);   // المبلغ النهائي بعد الخصم

            $table->string('payment_method')->default('Cash');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};