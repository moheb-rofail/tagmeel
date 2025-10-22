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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description', 255);
            $table->decimal('amount', 10, 2); // Stores the amount (e.g., 100.50)
            $table->date('expense_date');
            $table->string('category')->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_path')->nullable(); // Stores the file path for a receipt
            // $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Uncomment and run `composer dump-autoload` if you track users
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};