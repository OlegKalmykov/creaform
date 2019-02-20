<?php

namespace App\Api\Models;

use App\Exceptions\ApiException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class Department extends Model
{
    public $timestamps = false;

    /**
     * @var \App\Api\Models\User
     */
    private $user;

    private $departmentsToDelete = [];
    private $positionsToDelete = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'parent_id',
        'root',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->user = auth()->guard('api')->user();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    /**
     * @param      $name
     * @param null $parentId
     *
     * @throws ApiException
     */
    public function add($name, $parentId = null)
    {
        if (!is_null($parentId)) {
            self::checkDepartment($parentId, $this->user->id);

            $this->parent_id = $parentId;
        }

        $this->name = $name;
        $this->root = $this->getRootDepartment();

        if (!isset($this->parent_id)) {
            $this->parent_id = $this->root;
        }

        $this->save();
    }

    /**
     * @param $id
     * @param $name
     */
    public function rename($id, $name)
    {
        self::checkDepartment($id, $this->user->id);

        self::where('id', '=', $id)->update(['name' => $name]);
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        self::checkDepartment($id, $this->user->id);

        $departments = $this->getCompanyDepartments([
            'departments.id as department_id',
            'departments.parent_id',
            'positions.id as position_id',
        ]);

        $this->collectNodesForDelete($departments, $id);

        $this->removeScope();
        (new Position)->removeScope($this->positionsToDelete);
    }

    /**
     * @param $departments
     * @param $targetId
     */
    private function collectNodesForDelete($departments, $targetId)
    {
        $this->departmentsToDelete[] = $targetId;

        foreach ($departments as $department) {
            // собираем должности удаляемого подразделения
            if ($department->department_id == $targetId) {
                if (!is_null($department->position_id)) {
                    $this->positionsToDelete[] = $department->position_id;
                }
            }

            // собираем подчиненные подразделения
            if ($department->parent_id == $targetId) {
                $this->collectNodesForDelete($departments, $department->department_id);
            }
        }
    }

    /**
     *  Удаляет указанные в масиве подразделения
     */
    private function removeScope()
    {
        self::destroy($this->departmentsToDelete);
    }

    /**
     * @return array|bool
     */
    public function getUserCompany()
    {
        $departments = $this->getCompanyDepartments();

        if (empty($departments)) {
            return false;
        }

        // собираем список id подразделении от старшего подразделения к младшему
        $hierarchyParentIds = array_unique(array_column($departments, 'parent_id'));
        rsort($hierarchyParentIds);

        return $this->buildHierarchy($departments, $hierarchyParentIds);
    }

    /**
     * @param array $select
     *
     * @return array
     */
    private function getCompanyDepartments(array $select = [])
    {
        if (empty($select)) {
            $select = [
                'departments.id',
                'departments.name',
                'departments.parent_id',
                'positions.id as positions_id',
                'positions.name as positions',
                'companies.name as company_name',
            ];
        }

        $departments = DB::table('users')
            ->select($select)
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->join('departments', 'companies.department_id', '=', 'departments.root')
            ->leftJoin('positions', 'departments.id', '=', 'positions.department_id')
            ->where('users.id', '=', $this->user->id)
            ->get()->toArray();

        return $departments;
    }

    /**
     * @param $departments
     * @param $hierarchyParents
     *
     * @return array
     */
    private function buildHierarchy($departments, $hierarchyParents)
    {
        $tempDepartment = []; // поддерево узлов $parentId уровня
        $parentNodeIndex = 0; // index родительского элемента в массиве
        $parentId = array_shift($hierarchyParents);

        foreach ($departments as $index => $department) {
            if ($department->id == $parentId) {
                $parentNodeIndex = $index;
            }

            if ($department->parent_id == $parentId) {
                if (!isset($department->children)) {
                    $department->children = [];
                }

                if (!isset($tempDepartment[$department->id])) {
                    // Добавляем должность
                    $department->positions = !is_null($department->positions)
                        ? [['id' => intval($department->positions_id), 'name' => $department->positions]]
                        : [];

                    if (is_null($department->name)) {
                        $department->name = $department->company_name;
                    }

                    $tempDepartment[$department->id] = $department;
                } else {
                    // дополняем список должностей в уже записанном подразделении
                    $tempDepartment[$department->id]->positions[] = [
                        'id'   => intval($department->positions_id),
                        'name' => $department->positions,
                    ];

                    // дополняем список дочерних узлов
                    if (isset($tempDepartment[$department->id]->children)) {
                        $tempDepartment[$department->id]->children = array_merge(
                            $tempDepartment[$department->id]->children,
                            $department->children
                        );
                    }
                }

                unset(
                    $tempDepartment[$department->id]->parent_id,
                    $tempDepartment[$department->id]->positions_id,
                    $tempDepartment[$department->id]->company_name
                );

                if (!is_null($parentId)) {
                    unset($departments[$index]);
                }
            }
        }

        // Здесь достигнут корневой уровень, возвращаем все дерево
        if (is_null($parentId)) {
            return $tempDepartment;
        }

        $departments[$parentNodeIndex]->children = $tempDepartment;

        return $this->buildHierarchy($departments, $hierarchyParents);
    }

    /**
     * @return mixed|int
     */
    public function getRootDepartment()
    {
        $result = DB::table('users')
            ->select('companies.department_id')
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->where('users.id', '=', $this->user->id)
            ->first();

        return $result->department_id;
    }

    /**
     * @param $id
     * @param $userId
     *
     * @throws ApiException
     */
    public static function checkDepartment($id, $userId)
    {
        $result = DB::table('departments')
            ->select('users.id as user_id')
            ->leftJoin('companies', 'departments.root', '=', 'companies.department_id')
            ->leftJoin('users', 'companies.id', '=', 'users.company_id')
            ->where('departments.id', '=', $id)
            ->first();

        if (is_null($result)) {
            throw new ApiException(Lang::get('department.not_exist'), 400);
        }

        // если подразделение не принадлежит компании текущего юзера, то вбрасываем исключение на вентилятор
        if ($result->user_id != $userId) {
            throw new ApiException(Lang::get('department.wrong_department_id'), 400);
        }
    }
}
