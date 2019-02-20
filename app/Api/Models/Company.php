<?php

namespace App\Api\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    const SMALL = 1;
    const MIDDLE = 2;
    const BIG = 3;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'employees_count', 'department_id'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
