<?php

namespace App\Api\Models;

use App\Exceptions\ApiException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class Position extends Model
{
    public $timestamps = false;

    private $user;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'department_id',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->user = auth()->guard('api')->user();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * @param $name
     * @param null $departmentId
     * @throws ApiException
     */
    public function add($name, $departmentId = null)
    {
        $this->name = $name;

        if (!is_null($departmentId)) {
            Department::checkDepartment($departmentId, $this->user->id);

            $this->department_id = $departmentId;
        }

        $this->save();
    }

    /**
     * @param $id
     * @param $name
     */
    public function rename($id, $name)
    {
        $this->checkPosition($id);

        self::where('id', '=', $id)->update(['name' => $name]);
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $this->checkPosition($id);

        self::where('id', '=', $id)->delete();
    }

    /**
     * Удаляет список должностей
     * @param array $ids
     */
    public function removeScope(array $ids)
    {
        self::destroy($ids);
    }

    /**
     * @param $id
     * @throws ApiException
     */
    private function checkPosition($id)
    {
        $result = DB::table('positions')
            ->select('users.id as user_id')
            ->leftJoin('departments', 'positions.department_id', '=', 'departments.id')
            ->leftJoin('companies', 'departments.root', '=', 'companies.department_id')
            ->leftJoin('users', 'companies.id', '=', 'users.id')
            ->where('positions.id', '=', $id)
            ->first();

        if (is_null($result)) {
            throw new ApiException(Lang::get('position.not_exist'), 400);
        }

        // если должность не принадлежит компании текущего юзера, то вбрасываем исключение на вентилятор
        if ($result->user_id != $this->user->id) {
            throw new ApiException(Lang::get('position.wrong_department_id'), 400);
        }
    }
}
