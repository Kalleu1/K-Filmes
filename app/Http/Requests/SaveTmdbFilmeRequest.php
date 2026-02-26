<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveTmdbFilmeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nota' => 'nullable|numeric|min:0|max:10',
            'comentarios' => 'nullable|string|max:5000',
            'data_assistida' => 'nullable|date',
            'plataforma' => 'nullable|string|max:255',
            'assistido' => 'nullable|boolean',
        ];
    }
}
