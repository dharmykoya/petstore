<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'first_name'    => 'bail|required',
            'last_name'     => 'bail|required',
            'email'         => 'bail|required|email|unique:users',
            'password'      => 'bail|required|confirmed',
            'address'       => 'bail|required',
            'phone_number'  => 'bail|required|unique:users',
        ];
    }
}
