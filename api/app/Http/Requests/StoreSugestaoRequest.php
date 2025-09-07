<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSugestaoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'musica_id' => 'required|integer|exists:musicas,id',
            'comentario' => 'nullable|string|max:1000'
        ];
    }
}
