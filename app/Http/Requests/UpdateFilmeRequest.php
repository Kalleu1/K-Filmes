<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFilmeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plataforma' => 'nullable|string',
            'data_assistida' => 'nullable|date',
            'nota' => 'nullable|numeric|min:0|max:10',
            'comentarios' => 'nullable|string',
        ];
    }
}
