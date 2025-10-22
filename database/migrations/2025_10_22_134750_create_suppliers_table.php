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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('اسم المورد.');
            $table->string('contact_person')->nullable()->comment('الشخص المسؤول للتواصل.');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable()->comment('عنوان المورد.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
