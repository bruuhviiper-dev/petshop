<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgendamentoPublicoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'dono_nome'     => 'required|string|max:255',
            'dono_telefone' => 'required|string|max:20',
            'pet_nome'      => 'required|string|max:255',
            'pet_especie'   => 'required|string|max:100',
            'pet_raca'      => 'nullable|string|max:100',
            'servico_id'    => 'required|integer',
            'data'          => 'required|date|after_or_equal:today',
            'horario'       => 'required|date_format:H:i',
        ];
    }
}
