<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class EmpresasRequest extends FormRequest
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
            '*.iderp'    => 'required|integer',
            '*.cnpj'     => 'required|string|max:191',
            '*.razao'    => 'required|string|max:191',
            '*.fantasia' => 'required|string|max:191',
        ];
    }
}
