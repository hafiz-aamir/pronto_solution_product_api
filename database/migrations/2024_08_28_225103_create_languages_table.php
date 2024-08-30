<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Language;
use Carbon\Carbon;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            
            $table->id();
            $table->uuid('uuid')->unique(); // Add unique UUID column 
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('app_language_code')->unique();
            $table->boolean('rtl')->default(0);
            $table->string('auth_id')->default('1');
            $table->integer('status')->default('1');
            $table->timestamps();
            $table->softDeletes();

        });


        $now = Carbon::now();

        // Insert initial roles 
        $role = Language::insert([
            ['uuid' => Str::uuid(), 'name' => 'English', 'code' => 'us', 'app_language_code' => 'en', 'rtl' => '0', 'auth_id' => 'e6efa188-56d9-449c-b85c-75bf1f0d4f27' , 'created_at' => $now, 'updated_at' => $now]
        ]); 

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
