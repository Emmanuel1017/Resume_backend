<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ];


    }
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


    public function messages():array
    {
        return [
            'title.required'     => 'Give item a title please',
            'title.max'          => 'Tile is max of 50 characters',
            'description.required'    => 'Please give the item description',
        ];
    }
}
