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
        Schema::create('special_permissions', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('menu_id');
            $table->string('auth_id')->default('1');
            $table->integer('status')->default('1');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_permissions');
    }
};
