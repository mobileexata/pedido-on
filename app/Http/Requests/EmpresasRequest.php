<?php

namespace App\Http\Requests;

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
            '*.iderp'    => 'required|string',
            '*.cnpj'     => 'required|string|max:191',
            '*.razao'    => 'required|string|max:191',
            '*.fantasia' => 'required|string|max:191',
        ];
    }
}
