<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminEditUserRequest extends FormRequest
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
            "first_name" => 'sometimes|required',
            "last_name" => 'sometimes|required',
            "address" => 'sometimes|required',
            'email'   => 'sometimes|required|email|unique:users',
            'phone_number'   => 'sometimes|required|unique:users',
            'is_marketing' => 'sometimes|required|boolean'  ,
        ];
    }
}
