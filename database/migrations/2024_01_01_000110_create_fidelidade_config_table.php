<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fidelidade_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petshop_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('atendimentos_para_premio')->default(10);
            $table->decimal('desconto_percentual', 5, 2)->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fidelidade_config');
    }
};
