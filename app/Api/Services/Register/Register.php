<?php

namespace App\Api\Services\Register;

use App\Api\Models\Company;
use App\Api\Models\Department;
use App\Api\Models\Role;
use App\Api\Models\User;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Validator;

abstract class Register
{
    /**
     * @var array errors
     */
    protected $errors;

    /**
     * @var array incomingData
     */
    protected $data;

    /**
     * @var array common fields
     */
    protected $commonFields = [
        'email'     => 'required|email|unique:users',
        'password'  => 'required|confirmed|regex:(^[a-zA-Z0-9#$%\'^,*+:=?@\/\][_\-`{}\!\.;~]{6,50}$)',
        'agreement' => 'required|accepted'
    ];

    /**
     * @var array role fields
     */
    protected $roleFields = [];

    /**
     * @var \App\Api\Models\User user
     */
    protected $user;

    /**
     * @var string role
     */
    protected $role;

    public function register(array $data)
    {
        $this->validate($data);
        $this->createUser($data);
        $this->roleActions($data);
        $this->sendNotification();
    }

    protected function validate(array $data)
    {
        $validator = Validator::make($data, array_merge($this->commonFields));

        $errors = $validator->errors();

        if ($errors->isNotEmpty()) {
            $failed = $validator->failed();

            if ($errors->has('email') && array_key_exists('Unique', $failed['email'])) {
                $uniqueEmailError = $errors->get('email');
                throw new ApiException(reset($uniqueEmailError), 400);
            }

            throw new ValidationException($validator);
        }
    }

    /**
     * @param array $data
     */
    protected function createUser(array $data)
    {
        $this->user = User::create([
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'confirm_code' => str_random(64),
            'confirmed'    => 0
        ]);

        $role = Role::where('name', '=', $data['role'])->get();

        $this->user->roles()->attach($role);
    }

    protected function roleActions(array $data)
    {
    }

    /**
     * @param $companyName
     * @param $employeesCount
     */
    protected function createCompany($companyName, $employeesCount)
    {
        $department = Department::create(['parent_id' => null]);

        Department::updateOrInsert(['id' => $department->id], ['root' => $department->id]);

        $company = Company::create([
            'name'            => $companyName,
            'employees_count' => $employeesCount,
            'department_id'   => $department->id
        ]);

        $this->user->company()->associate($company);

        $this->user->save();
    }

    protected function sendNotification()
    {
        $this->user->sendConfirmRegisterNotification($this->user->confirm_code);
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }
}