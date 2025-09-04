<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auth_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('kind')->nullable(); // passkey_setup, password_creation
            $table->integer('duration_ms')->nullable(); // tiempo en milisegundos
            $table->boolean('success')->default(false);
            $table->string('error_code')->nullable(); // código de error (si hay)
            $table->text('error_message')->nullable(); // detalle del error
            $table->string('user_id')->nullable(); // ID WorkOS del usuario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_metrics');
    }
};
