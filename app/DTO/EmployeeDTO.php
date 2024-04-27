<?php

namespace App\DTO;

use App\Enums\ProviderEnum;

class EmployeeDTO 
{

    public function __construct(
        public array $data
    ) {}

    public function mapData(): array
    {
        switch ($this->data['provider']) {
            case ProviderEnum::EMPLOYEE_PROVIDER_ONE->value:
                $payload = $this->providerOneData();
                break;
            case ProviderEnum::EMPLOYEE_PROVIDER_TWO->value:
                $payload = $this->providerTwoData();
                break;
        }

        if (isset($this->data['employee_id'])) {
            $payload['id'] = $this->data['employee_id'];
        }
        
        return $payload;

    }

    private function providerOneData(): array
    {
        return [
            'firstName' => $this->data['first_name'],
            'lastName' => $this->data['last_name'],
            'email' => $this->data['email']
        ];
    }

    private function providerTwoData(): array
    {
        return [
            'firstName' => $this->data['FirstName'],
            'lastName' => $this->data['LastName'],
            'email' => $this->data['EmailAddress']
        ];
    }   

}