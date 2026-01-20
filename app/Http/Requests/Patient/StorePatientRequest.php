<?php

declare(strict_types=1);

namespace App\Http\Requests\Patient;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StorePatientRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z]+$/'],
            'email' => ['required', 'email:rfc,dns', 'string', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', 'min:8', 'max:88'],
        ];
    }
}
