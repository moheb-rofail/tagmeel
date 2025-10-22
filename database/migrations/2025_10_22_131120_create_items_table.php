<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الترحيلات.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            
            // Product Identification
            $table->string('name')->unique()->comment('اسم المنتج (يجب ان يكون فريد)');
            $table->text('description')->nullable()->comment('وصف المنتج');

            // Mandatory Inventory Management Logic 
            $table->unsignedBigInteger('stock_quantity')->default(0)->comment('الكمية الحالية المتوفرة في المخزون.');
            $table->unsignedBigInteger('reorder_point')->default(5)->comment('أقل مستوى للمخزون قبل الحاجة لإعادة الطلب.');

            // Mandatory Pricing Information 
            $table->decimal('unit_price', 10, 2)->default(0.00)->comment('سعر التكلفة (ما نشتريه به).');
            $table->decimal('selling_price', 10, 2)->default(0.00)->comment('سعر البيع المعلن.');
            
            $table->timestamps();
        });
    }

    /**
     * التراجع عن الترحيلات.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
