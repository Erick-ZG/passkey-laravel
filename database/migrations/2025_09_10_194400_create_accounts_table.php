<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // dueño
            $table->string('number', 24)->unique();                         // número visible
            $table->string('name');                                         // alias: “Cuenta principal”
            $table->char('currency', 3)->default('USD');
            $table->decimal('balance', 18, 2)->default(0);                  // saldo disponible
            $table->boolean('is_primary')->default(false);
            $table->enum('status', ['active','blocked','closed'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
