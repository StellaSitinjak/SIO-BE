<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username'      => 'required|string|min:6',
            'image'         => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone_number'  => 'required|string',
            'first_name'    => 'required|string|min:2',
            'last_name'     => 'required|string|min:2',
            'date_of_birth' => 'required',
        ];
    }
}
