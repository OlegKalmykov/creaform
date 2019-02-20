<?php

use Illuminate\Database\Seeder;
use App\Api\Models\User;
use App\Api\Models\Role;

class UserTableSeeder extends Seeder
{
    private $roles;
    private $counter = 1;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->roles = Role::all();

        $this->createAdministratorUsers();
        $this->createOrganizationUsers();
        $this->createStaffUsers();
        $this->createPrivateCustomerUsers();
        $this->createCandidateUsers();
        $this->createSpecialistUsers();
        $this->createPartnerUsers();
    }

    private function createAdministratorUsers()
    {
        $users = ['admin'];

        $this->createUser($users, ['admin', 'company'], 1);
    }

    private function createOrganizationUsers()
    {
        $users = ['igor'];

        $this->createUser($users, 'company');
    }

    private function createStaffUsers()
    {
        $users = ['michael', 'janna'];

        $this->createUser($users, 'staff');
    }

    private function createPrivateCustomerUsers()
    {
        $users = ['alex', 'wendy'];

        $this->createUser($users, 'private customer');
    }

    private function createCandidateUsers()
    {
        $users = ['tom', 'phill'];

        $this->createUser($users, 'candidate');
    }

    private function createSpecialistUsers()
    {
        $users = ['specialist'];

        $this->createUser($users, 'specialist');
    }

    private function createPartnerUsers()
    {
        $users = ['partner'];

        $this->createUser($users, 'partner');
    }

    /**
     * Отрефакторить структуру параметро в массивы
     * @param array $names
     * @param $roleName
     * @param int $confirmed
     */
    private function createUser(array $names, $roleName, $confirmed = 0)
    {
        foreach ($names as $name) {
            $employee = new User();
            $employee->name = $name;
            $employee->email = $name.'@example.com';
            $employee->password = bcrypt($name);
            $employee->confirmed = $confirmed;
            $employee->save();

            if (is_array($roleName)) {
                foreach ($roleName as $role) {
                    $employee->roles()->attach($this->roles->where('name', $role));
                }
            } else {
                $employee->roles()->attach($this->roles->where('name', $roleName));
            }
        }
    }
}
