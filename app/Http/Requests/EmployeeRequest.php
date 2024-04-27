<?php

namespace App\Http\Requests;

use App\Enums\ProviderEnum;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmployeeRequest extends FormRequest
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
        $rules = [];

        $rules['provider'] = ['required', 'string', Rule::in(ProviderEnum::cases())];

        switch ($this->provider) {
            case ProviderEnum::EMPLOYEE_PROVIDER_ONE->value:
                $rules['first_name'] = ['required', 'string', 'min:3'];
                $rules['last_name'] = ['required', 'string', 'min:3'];
                $rules['email_address'] = ['required', 'string', 'email'];
                break;
            case ProviderEnum::EMPLOYEE_PROVIDER_TWO->value:
                $rules['FirstName'] = ['required', 'string', 'min:3'];
                $rules['LastName'] = ['required', 'string', 'min:3'];
                $rules['EmailAddress'] = ['required', 'string', 'email'];
                break;
        }

        if ($this->method() === 'PATCH') {
            $rules['employee_id'] = ['required', 'integer'];
        }
        
        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message'   => 'There is one or more validation errors',
            'errors'      => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
