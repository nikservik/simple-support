<?php

namespace Nikservik\SimpleSupport\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportMessage extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => 'required|string|min:2',
        ];
    }

    public function messages()
    {
        return [
            'message.required' => 'message.required',
            'message.string' => 'message.string',
            'message.min' => 'message.min',
        ];
    }
}
