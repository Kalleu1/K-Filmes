<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFilmeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'plataforma' => 'nullable|string',
            'data_assistida' => 'nullable|date',
            'diretor' => 'nullable|string|max:255',
            'genero' => 'nullable|string|max:255',
            'nota' => 'nullable|numeric|min:0|max:10',
            'comentarios' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'poster_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
