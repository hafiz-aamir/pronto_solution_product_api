<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Menu;
use Carbon\Carbon;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->integer('sort_id')->default('1');
            $table->longText('icon')->nullable();
            $table->string('auth_id')->default('1');
            $table->integer('status')->default('1');
            $table->integer('parent_id')->default('1');
            $table->timestamps(); 
            $table->softDeletes();

        });

        $now = Carbon::now();

        // Insert initial roles 
        $role = Menu::insert([
            ['uuid' => Str::uuid(), 'name' => 'Menu', 'description' => '', 'sort_id' => '1', 'icon' => '', 'auth_id' => 'e6efa188-56d9-449c-b85c-75bf1f0d4f27'  ,  'parent_id' => '0' , 'created_at' => $now, 'updated_at' => $now],
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
