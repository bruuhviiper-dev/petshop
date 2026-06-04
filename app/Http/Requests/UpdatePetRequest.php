<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePetRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'species'      => 'required|string|max:100',
            'breed'        => 'nullable|string|max:100',
            'weight'       => 'nullable|numeric|min:0',
            'temperament'  => 'nullable|string|max:100',
            'allergies'    => 'nullable|string',
            'notes'        => 'nullable|string',
            'photo'        => 'nullable|image|max:2048',
            'retorno_dias' => 'nullable|integer|min:1',
        ];
    }
}
