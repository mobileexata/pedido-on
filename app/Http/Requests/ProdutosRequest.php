<?php

namespace App\Http\Requests;

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
            '*.iderp' => ['required', 'string'],
            '*.idempresaerp' => ['required', 'string'],
            '*.nome' => ['required', 'string'],
            '*.referencia' => ['nullable', 'string'],
            '*.estoque' => ['present', 'numeric'],
            '*.fabricante_id' => ['required', 'string'],

            '*.precos' => ['required', 'array'],
            '*.precos.*.vlpreco' => ['required', 'numeric'],
            '*.precos.*.codtipopreco' => ['required', 'integer'],

            '*.custos' => ['required', 'array'],
            '*.custos.*.vlcusto' => ['required', 'numeric'],
            '*.custos.*.codempresa' => ['required', 'integer'],

            '*.grupo' => ['required', 'array'],
            '*.grupo.codgrupo' => ['required', 'integer'],
            '*.grupo.descgrupo' => ['required', 'string'],
            '*.grupo.codsubgrupo' => ['required', 'integer'],
            '*.grupo.descsubgrupo' => ['required', 'string']
        ];
    }
}
