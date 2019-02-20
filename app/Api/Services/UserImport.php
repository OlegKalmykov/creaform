<?php

namespace App\Api\Services;

use App\Api\Models\Department;
use App\Api\Models\User;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserImport
{
    private $user;
    private $companyId;

    /**
     * [
     *   0 => array:3 [
     *         0 => "surname"
     *         1 => "name"
     *         2 => "patronymic"
     *         3 => "000-026"
     *         4 => "NZfDGgXj"
     *    ],
     *  ...
     * ]
     * @var array
     */
    private $employees = [];

    public function __construct(User $user, Department $department)
    {
        $this->user = $user;
        $this->companyId = $department->getRootDepartment();
    }

    /**
     * @param $filePath
     * @return array
     * @throws ApiException
     */
    public function run($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $this->employees = array_slice($sheet->toArray(), 1);

        if (empty($this->employees)) {
            throw new ApiException(Lang::get('exceptions.file_empty'), 400);
        }

        $this->createUsers();

        return $this->employees;
    }

    /**
     *  Создает пользователей в БД, генерирует им логины и пароли
     */
    public function createUsers()
    {
        $records = [];
        // формируем массив для первичной записи сотрудников в БД
        foreach ($this->employees as $index => $employee) {
            $password = str_random(8);
            $employee[] = $password;

            $passwordHash = Hash::make($password);

            $this->employees[$passwordHash] = $employee;
            unset($this->employees[$index]);

            $records[] = [
                'email'      => null,
                'company_id' => $this->companyId,
                'password'   => $passwordHash,
                'confirmed'  => 1
            ];
        }

        DB::table($this->user->getTable())->insert($records);

        $users = DB::table($this->user->getTable())
            ->select(['id', 'password'])
            ->whereIn('password', array_keys($this->employees))
            ->get();

        foreach ($users as $index => $user) {
            // записываем шифр юзера в массив, который отдадим на формирование pdf
            $userLoginCode = $this->user->getCode($user->id);
            $password = array_pop($this->employees[$user->password]);

            $this->employees[$user->password][] = $userLoginCode;
            $this->employees[$user->password][] = $password;

            // и в массив, который обновит таблицу юзеров
            $users[$index]->login = $userLoginCode;

            // перезаписываем массив с удаление хэшей паролей
            $this->employees[] = $this->employees[$user->password];
            unset($this->employees[$user->password]);

            $insertValues[] = "(".$user->id.", '".$user->login."', '".$user->password."')";
        }

        DB::insert(
            'INSERT INTO `users` (id, login, password) VALUES'.implode(', ', $insertValues).' '.
            'ON DUPLICATE KEY UPDATE login = VALUES(login)'
        );
    }
}