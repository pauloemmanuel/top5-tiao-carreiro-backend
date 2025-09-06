<?php

namespace App\Http\Requests\Sugestao;

use Illuminate\Foundation\Http\FormRequest;

class ProcessarSugestaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'observacoes' => ['nullable', 'string', 'max:500']
        ];
    }

    public function messages(): array
    {
        return [
            'observacoes.string' => 'As observações devem ser um texto',
            'observacoes.max' => 'As observações não podem ter mais que 500 caracteres'
        ];
    }
}
