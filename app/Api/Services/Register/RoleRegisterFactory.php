<?php

namespace App\Api\Services\Register;

use App\Api\Models\Role;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Lang;

final class RoleRegisterFactory
{
    /**
     * @param $role
     * @return CompanyRegister|IndividualRegister|PartnerRegister
     * @throws ApiException
     */
    public static function create($role)
    {
        switch ($role) {
            case Role::COMPANY:
                return new CompanyRegister();
                break;

            case Role::PARTNER:
                return new PartnerRegister();
                break;

            case Role::INDIVIDUAL:
                return new IndividualRegister();
                break;

            default:
                throw new ApiException(Lang::get('exceptions.incorrect_role'), 400);
                break;
        }
    }
}