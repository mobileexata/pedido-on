<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendaRequest extends FormRequest
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
            'empresa_id' => 'required',
            'cliente_id' => 'required',
            'tiposvenda_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'empresa_id.required' => 'Selecione a empresa do pedido',
            'cliente_id.required' => 'Selecione o cliente do pedido',
            'tiposvenda_id.required' => 'Selecione a forma de pagamento do pedido',
        ];
    }

}
