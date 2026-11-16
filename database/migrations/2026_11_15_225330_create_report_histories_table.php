<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();

            $table->dateTime('occurred_at');                  // when the action happened
            $table->string('status', 20);                     // status AFTER the action
            $table->foreignId('office_id')->constrained()->restrictOnDelete();   // office responsible AFTER the action
            $table->text('remarks')->nullable();              // the note

            $table->foreignId('acted_by_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('acted_by_office_id')->nullable()->constrained('offices')->restrictOnDelete();

            $table->timestamp('created_at')->useCurrent();    // history is never edited, so no updated_at

            // Fast lookup of one report's timeline in order
            $table->index(['report_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_histories');
    }
};