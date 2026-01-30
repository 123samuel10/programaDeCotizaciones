<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required','string','max:255'],
            'email'     => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],

            // ✅ Campos para cotización
            'empresa'   => ['nullable','string','max:255'],
            'pais'      => ['nullable','string','max:100'],
            'ciudad'    => ['nullable','string','max:120'],
            'direccion' => ['nullable','string','max:255'],
            'telefono'  => ['nullable','string','max:40'],
            'nit'       => ['nullable','string','max:60'],
        ];
    }
}
