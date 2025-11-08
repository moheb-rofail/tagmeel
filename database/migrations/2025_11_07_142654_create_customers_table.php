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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            
            // الرصيد الافتتاحي (اختياري)
            $table->decimal('initial_balance', 10, 2)->default(0); 
            
            // الرصيد الحالي (الأهم)
            $table->decimal('current_balance', 10, 2)->default(0); 
            $table->enum('balance_type', ['Debit', 'Credit'])->default('Debit'); // مدين (عليه) أو دائن (له)
            
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};