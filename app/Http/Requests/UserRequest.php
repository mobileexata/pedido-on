<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', Rule::unique((new User)->getTable())->ignore($this->route('user') ?? null)],
            'password' => ($this->method() == 'PUT' and !$this->password) ? [] : ['required', 'string', 'min:8', 'confirmed'],
            'iderp' => ['required', 'integer', 'max:191'],
            'meta' => ['required']
        ];
    }

}
