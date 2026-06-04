<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePetRequest extends FormRequest
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
            'photo'        => [
                'nullable',
                'file',
                'max:2048',
                function (string $attr, mixed $value, \Closure $fail): void {
                    $mime = $value->getMimeType(); // detecta pelo conteúdo real, não extensão
                    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                        $fail('O arquivo deve ser uma imagem JPEG, PNG ou WebP.');
                    }
                },
            ],
            'retorno_dias' => 'nullable|integer|min:1',
        ];
    }
}
