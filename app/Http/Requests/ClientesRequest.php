<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientesRequest extends FormRequest
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
            'clientes' => ['required', 'array'],
            'clientes.*.iderp' => ['required', 'integer'],
            'clientes.*.idempresaerp' => ['required', 'string'],
            'clientes.*.idrotaerp' => ['required', 'string'],
            'clientes.*.nome' => ['required', 'string'],
            'clientes.*.documento' => ['required', 'string'],
            'clientes.*.ativo' => ['required', 'string', 'in:S,N'],
            'clientes.*.saldo_pendente' => [ 'required', 'numeric'],
        ];
    }
}
