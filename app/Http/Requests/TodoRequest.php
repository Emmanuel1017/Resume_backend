<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:50',
            'email'    => 'required|max:255|email|unique:users',
            'password' => 'required|confirmed',
            'image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
