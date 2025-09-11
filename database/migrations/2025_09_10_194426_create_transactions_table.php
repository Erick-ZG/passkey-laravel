<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performed_by_user_id')->constrained('users'); // quién ejecutó
            $table->enum('type', ['deposit','withdrawal','transfer_in','transfer_out','payment']);
            $table->decimal('amount', 18, 2);                     // siempre positivo
            $table->decimal('balance_after', 18, 2);              // snapshot
            $table->string('reference')->nullable();              // ref externa/nota
            $table->string('counterparty')->nullable();           // nombre/número contraparte
            $table->enum('status', ['completed','pending','failed','reversed'])->default('completed');
            $table->foreignId('related_transaction_id')->nullable()->constrained('transactions')->nullOnDelete(); // par de transferencias
            $table->json('metadata')->nullable();                 // datos adicionales (merchant_id, etc.)
            $table->softDeletes();
            $table->timestamps();

            $table->index(['account_id','type','created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
