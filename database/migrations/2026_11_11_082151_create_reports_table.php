<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // 1. Identity
            $table->string('report_number', 30)->unique();   // e.g. RDAC-2026-00001

            // 2. What happened
            $table->string('subject', 150)->nullable();
            $table->foreignId('complaint_type_id')->constrained()->restrictOnDelete();
            $table->text('description');

            // 3. When and by whom it was filed
            $table->dateTime('filed_at');
            $table->foreignId('filed_by_user_id')->nullable()->constrained('users')->restrictOnDelete();

            // 4. The complainant
            $table->boolean('is_anonymous')->default(false);
            $table->string('complainant_name', 100)->nullable();
            $table->string('complainant_phone', 11)->nullable();
            $table->string('complainant_email', 100)->nullable();

            // 5. Location of the incident
            $table->string('barangay', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 50)->nullable();

            // 6. Workflow
            $table->string('status', 20)->default('pending');
            $table->foreignId('initial_office_id')->constrained('offices')->restrictOnDelete();
            $table->foreignId('current_office_id')->constrained('offices')->restrictOnDelete();
            $table->dateTime('resolved_at')->nullable();

            // 7. Archive (empty = not archived)
            $table->dateTime('archived_at')->nullable();
            $table->foreignId('archived_by_user_id')->nullable()->constrained('users')->restrictOnDelete();

            $table->timestamps();

            // Indexes: make searching and filtering these columns fast
            $table->index('status');
            $table->index('filed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};