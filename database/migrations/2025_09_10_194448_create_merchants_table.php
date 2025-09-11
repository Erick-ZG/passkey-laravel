<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();       // "Utilities", "Internet", etc.
            $table->string('billing_reference')->nullable(); // nro cliente/servicio
            $table->enum('status', ['active','inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status','name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
