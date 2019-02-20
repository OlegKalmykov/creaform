<?php

namespace App\Api\Services\Register;



class CompanyRegister extends Register
{
    protected $roleFields = [
        'company'         => 'required|string',
        'employees_count' => 'required|in(1,2,3)'
    ];

    /**
     * @param array $data
     */
    protected function roleActions(array $data)
    {
        $this->createCompany($data['company'], $data['employees_count']);
    }
}