<?php

use App\Api\Models\Company;
use App\Api\Models\User;
use App\Api\Models\Department;
use App\Api\Models\Position;
use Illuminate\Database\Seeder;

class CompanyStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createCompany();
        $this->createCompanyStructure();
    }

    public function createCompany()
    {
        Company::create([
            'name'            => 'StartUp',
            'employees_count' => 1,
            'department_id'   => 1
        ]);

        User::where('id', '=', 1)->update(['company_id' => 1]);
    }

    public function createCompanyStructure()
    {
        Department::create(['root' => 1]);
        Position::create(['name' => 'CEO', 'department_id' => 1]);

        $web = Department::create(['name' => 'Web', 'parent_id' => 1, 'root' => 1]);
        Position::create(['name' => 'Middle developer', 'department_id' => $web->id]);
        Position::create(['name' => 'Junior Borsch developer', 'department_id' => $web->id]);

        Department::create(['name' => 'Finance', 'parent_id' => 1, 'root' => 1]);
        $sysAdm = Department::create(['name' => 'System Department', 'parent_id' => 1, 'root' => 1]);
        Position::create(['name' => 'System Administrator', 'department_id' => $sysAdm->id]);

        $backend = Department::create(['name' => 'Backend', 'parent_id' => $web->id, 'root' => 1]);
        $frontend = Department::create(['name' => 'Frontend', 'parent_id' => $web->id, 'root' => 1]);
        Position::create(['name' => 'React developer', 'department_id' => $frontend->id]);

        $testing = Department::create(['name' => 'Testing', 'parent_id' => $backend->id, 'root' => 1]);
        Position::create(['name' => 'Tester', 'department_id' => $testing->id]);

        $internship = Department::create(['name' => 'Internship', 'parent_id' => $testing->id, 'root' => 1]);
        Position::create(['name' => 'Intern', 'department_id' => $internship->id]);
        Position::create(['name' => 'Mature Intern', 'department_id' => $internship->id]);
    }
}
