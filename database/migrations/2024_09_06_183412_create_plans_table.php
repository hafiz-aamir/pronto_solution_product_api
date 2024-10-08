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
           
            $table->id(); 
            $table->uuid('uuid')->unique();
            $table->string('name', 255); 
            $table->longText('description')->nullable(); 
            $table->string('type', 255); 
            $table->string('amount', 255); 
            $table->boolean('status')->default(1); 
            $table->string('auth_id')->default(1);
            $table->timestamps();

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
