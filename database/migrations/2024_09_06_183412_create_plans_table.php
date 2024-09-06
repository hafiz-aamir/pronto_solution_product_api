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
        Schema::create('plans', function (Blueprint $table) {
           
            $table->id(); // id (int, primary key, auto-increment)
            $table->uuid('uuid')->unique();
            $table->string('name', 255); // name (varchar 255)
            $table->longText('description')->nullable(); // description (longText)
            $table->string('type', 255); // type (varchar 255)
            $table->string('amount', 255); // amount (varchar 255)
            $table->boolean('status')->default(1); // status (boolean, default to 1)
            $table->string('auth_id')->default(1); // auth_id (unsigned big integer, default to 1)
            $table->timestamps(); // created_at and updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
