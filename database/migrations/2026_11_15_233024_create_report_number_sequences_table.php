<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_number_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();   // 2026 is the key itself
            $table->unsignedInteger('last_number')->default(0); // the last number handed out
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_number_sequences');
    }
};