<?php

use App\Api\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createAdmin();
        $this->createOrganization();
        $this->createOrganizationStaff();
        $this->createPrivateCustomer();
        $this->createCandidate();
        $this->createSpecialist();
        $this->createPartner();
    }

    private function createAdmin()
    {
        $roleAdministrator = new Role();
        $roleAdministrator->name = 'admin';
        $roleAdministrator->description = 'Администратор сервиса';
        $roleAdministrator->save();
    }

    private function createOrganization()
    {
        $roleOrganization = new Role();
        $roleOrganization->name = 'company';
        $roleOrganization->description = 'Компания';
        $roleOrganization->save();
    }

    private function createOrganizationStaff()
    {
        $roleOrganizationEmployee = new Role();
        $roleOrganizationEmployee->name = 'staff';
        $roleOrganizationEmployee->description = 'Персонал компании';
        $roleOrganizationEmployee->save();
    }

    private function createPrivateCustomer()
    {
        $roleOrganizationEmployee = new Role();
        $roleOrganizationEmployee->name = 'private customer';
        $roleOrganizationEmployee->description = 'Частный клиент';
        $roleOrganizationEmployee->save();
    }

    private function createCandidate()
    {
        $roleOrganizationPsychologist = new Role();
        $roleOrganizationPsychologist->name = 'candidate';
        $roleOrganizationPsychologist->description = 'Кандидат / соискатель';
        $roleOrganizationPsychologist->save();
    }

    private function createSpecialist()
    {
        $roleOrganizationPsychologist = new Role();
        $roleOrganizationPsychologist->name = 'specialist';
        $roleOrganizationPsychologist->description = 'Специалист сервиса';
        $roleOrganizationPsychologist->save();
    }

    private function createPartner()
    {
        $roleOrganizationPsychologist = new Role();
        $roleOrganizationPsychologist->name = 'partner';
        $roleOrganizationPsychologist->description = 'HR-фгентство';
        $roleOrganizationPsychologist->save();
    }
}
