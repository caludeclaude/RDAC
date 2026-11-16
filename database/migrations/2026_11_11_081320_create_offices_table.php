<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();                                // the primary key
            $table->string('name', 100)->unique();       // VARCHAR(100), no duplicates
            $table->string('office_type', 20);           // regional / penro / cenro / division
            $table->string('province', 50)->nullable();  // ->nullable() means "may be empty"
            $table->string('city', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();                        // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};