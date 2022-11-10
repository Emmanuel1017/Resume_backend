<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize():bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules():array
    {
        return [
            'name' => 'required|string|max:255',
             'user_type' => 'string',
             'email' => 'required|string|email|max:255|unique:users',
             'password' => 'required|string|min:6',
        ];
    }
    public function messages(): array
    {
        return [
            'email.required'  => 'Please give your email',
            'name.required'       => 'name required',
            'password.required'       => 'Password required',
        ];
    }
}
