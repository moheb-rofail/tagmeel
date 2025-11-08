<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('payment_status', ['Not Paid', 'Partial', 'Paid'])
                ->default('Not Paid')
                ->after('payment_method');
            $table->decimal('paid_amount', 10, 2)->default(0.00)->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'paid_amount']);
        });
    }
};