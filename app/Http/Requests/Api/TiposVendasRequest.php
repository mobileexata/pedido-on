<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TiposVendasRequest extends FormRequest
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
            '*.iderp' => 'required|integer',
            '*.idempresaerp' => 'required|string',
            '*.nome' => 'required|string',
            '*.ativo' => 'required|string|in:S,N',
        ];
    }
}
