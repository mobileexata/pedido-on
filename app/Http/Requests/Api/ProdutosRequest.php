<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProdutosRequest extends FormRequest
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
            '*.referencia' => 'required|string',
            '*.preco' => 'required|numeric',
            '*.estoque' => 'required|integer',
            '*.ativo' => 'required|string|in:S,N',
            '*.ean' => 'required|string',
        ];
    }
}
