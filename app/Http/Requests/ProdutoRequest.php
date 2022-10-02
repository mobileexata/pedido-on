<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoRequest extends FormRequest
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
            'imagem' => ['required', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,svg'],
            'imagem.*' => ['required', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,svg']
        ];
    }

}
