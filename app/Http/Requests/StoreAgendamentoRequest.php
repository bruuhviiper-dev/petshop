<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgendamentoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cliente_id'     => 'required|exists:clientes,id',
            'pet_id'         => 'required|exists:pets,id',
            'servico_id'     => 'required|exists:servicos,id',
            'colaborador_id' => 'nullable|exists:colaboradores,id',
            'scheduled_at'   => 'required|date|after:now',
            'notes'          => 'nullable|string|max:1000',
        ];
    }
}
