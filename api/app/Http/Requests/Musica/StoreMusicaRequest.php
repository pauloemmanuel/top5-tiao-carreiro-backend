<?php

namespace App\Http\Requests\Musica;

use Illuminate\Foundation\Http\FormRequest;

class StoreMusicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url_youtube' => [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'url_youtube.required' => 'A URL do YouTube é obrigatória',
            'url_youtube.url' => 'A URL fornecida não é válida',
            'url_youtube.regex' => 'A URL deve ser do YouTube no formato correto'
        ];
    }
}
