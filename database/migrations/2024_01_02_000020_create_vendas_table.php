<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petshop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // quem vendeu
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('desconto', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('payment_method', ['dinheiro', 'pix', 'debito', 'credito'])->default('dinheiro');
            $table->string('status')->default('concluida'); // concluida | cancelada
            $table->timestamps();

            $table->index(['petshop_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
