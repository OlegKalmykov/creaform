<?php

namespace App\Api\Services\Register;

use App\Api\Models\Company;

class PartnerRegister extends Register
{
    protected $roleFields = [
        'company'         => 'required|string',
        'employees_count' => 'required|in(1,2,3)'
    ];

    protected function roleActions(array $data)
    {
        $this->createCompany($data['company'], $data['employees_count']);
    }
}