<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100);
            $table->string('username', 64)->unique();
            $table->string('email', 100)->nullable()->unique();
            $table->string('phone', 11)->nullable();
            $table->string('role', 20);

            // THE LINK: office_id points to offices.id
            $table->foreignId('office_id')->constrained()->restrictOnDelete();

            $table->string('password');
            $table->boolean('must_change_password')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Laravel stores logins ("sessions") here, replacing your old cache tokens
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};