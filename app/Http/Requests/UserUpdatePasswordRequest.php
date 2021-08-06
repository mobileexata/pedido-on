<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => [
                'required', 'string', 'min:8', 'confirmed'
            ],
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Informe a nova senha',
            'password.string' => 'Senha inválida',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres',
            'password.confirmed' => 'As senhas não conferem',
        ];
    }

}
