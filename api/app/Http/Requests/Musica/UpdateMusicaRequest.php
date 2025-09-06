<?php

namespace App\Http\Requests\Musica;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMusicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['sometimes', 'string', 'max:255'],
            'visualizacoes' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', Rule::in(['ativa', 'inativa'])],
            'url_youtube' => [
                'sometimes',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.string' => 'O título deve ser um texto',
            'titulo.max' => 'O título não pode ter mais que 255 caracteres',
            'visualizacoes.integer' => 'O número de visualizações deve ser um número inteiro',
            'visualizacoes.min' => 'O número de visualizações não pode ser negativo',
            'status.in' => 'O status deve ser ativa ou inativa',
            'url_youtube.url' => 'A URL fornecida não é válida',
            'url_youtube.regex' => 'A URL deve ser do YouTube no formato correto'
        ];
    }
}
