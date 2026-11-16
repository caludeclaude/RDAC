<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('original_name', 255);   // the file name the user uploaded
            $table->string('stored_path', 500);     // where Laravel saved it (private folder)
            $table->string('mime_type', 100);       // PDF, JPEG or PNG
            $table->unsignedInteger('file_size');   // size in bytes
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_attachments');
    }
};