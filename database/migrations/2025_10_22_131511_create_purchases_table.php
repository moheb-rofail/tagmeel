// database/migrations/YYYY_MM_DD_HHMMSS_create_purchases_table.php

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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            
            // الربط بالمورد
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            
            $table->date('purchase_date');
            $table->string('invoice_number')->unique()->nullable();
            
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('Pending'); // Paid, Partial, Pending
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};