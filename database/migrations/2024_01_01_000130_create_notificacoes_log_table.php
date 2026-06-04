<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agendamento_id')->constrained('agendamentos')->cascadeOnDelete();
            $table->enum('type', ['confirmacao', 'lembrete_24h', 'lembrete_1h', 'avaliacao', 'retorno', 'fidelidade']);
            $table->dateTime('sent_at');
            $table->enum('status', ['enviado', 'falha']);
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes_log');
    }
};
