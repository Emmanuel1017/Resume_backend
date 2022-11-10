<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoAddRequest extends FormRequest
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
            'id' => 'integer',
            'title' => 'required|string|max:255',
            'user_id' => 'string',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ];
    }


    public function messages():array
    {
        return [
            'title.required'     => 'Give item a title please ---',
            'title.max'          => 'Tile is max of 50 characters',
            'image.required'  =>  'image is required',
            'description.required'    => 'Please give the item description',
        ];
    }
}
