<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estoque_movimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petshop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('produto_id')->constrained('produtos')->cascadeOnDelete();
            $table->enum('type', ['entrada', 'saida', 'ajuste', 'venda']);
            $table->integer('quantity');            // positivo entrada, negativo saída
            $table->integer('stock_after');         // estoque resultante
            $table->string('reason')->nullable();
            $table->foreignId('venda_id')->nullable()->constrained('vendas')->nullOnDelete();
            $table->timestamps();

            $table->index(['petshop_id', 'produto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estoque_movimentos');
    }
};
