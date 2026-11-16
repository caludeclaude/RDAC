<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_routings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');         // 1 = first office, 2 = second...
            $table->foreignId('office_id')->constrained()->restrictOnDelete();
            $table->foreignId('routed_by_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('routed_at');
            $table->timestamp('created_at')->useCurrent();

            // A report cannot have two rows with the same position
            $table->unique(['report_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_routings');
    }
};