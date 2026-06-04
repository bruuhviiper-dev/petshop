<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgendamentoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cliente_id'     => 'sometimes|exists:clientes,id',
            'pet_id'         => 'sometimes|exists:pets,id',
            'servico_id'     => 'sometimes|exists:servicos,id',
            'colaborador_id' => 'nullable|exists:colaboradores,id',
            'scheduled_at'   => 'sometimes|date',
            'status'         => 'sometimes|in:pendente,confirmado,em_andamento,concluido,cancelado',
            'notes'          => 'nullable|string|max:1000',
        ];
    }
}
