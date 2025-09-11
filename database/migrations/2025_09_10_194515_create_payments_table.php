<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();      // desde
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();     // hacia
            $table->decimal('amount', 18, 2);
            $table->string('description')->nullable();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();  // vínculo al ledger
            $table->enum('status', ['completed','pending','failed'])->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
