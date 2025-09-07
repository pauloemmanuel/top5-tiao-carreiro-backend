<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMusicaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'titulo' => 'sometimes|required|string|max:255',
            'artista' => 'sometimes|required|string|max:255',
            'ano' => 'nullable|integer'
        ];
    }
}
