<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Permission_assign;
use Carbon\Carbon;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permission_assigns', function (Blueprint $table) {
           
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('role_id');
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
        Schema::dropIfExists('permission_assigns');
    }
};
