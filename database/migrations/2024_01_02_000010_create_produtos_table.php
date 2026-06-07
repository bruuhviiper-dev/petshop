<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petshop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();          // código / código de barras
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2)->default(0);  // preço de venda
            $table->decimal('cost', 10, 2)->default(0);   // custo (para margem/lucro)
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock')->default(0);     // alerta de estoque baixo
            $table->string('unit')->default('un');        // un, kg, cx...
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['petshop_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
