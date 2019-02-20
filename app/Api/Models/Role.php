<?php

namespace App\Api\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const COMPANY = 'company';
    const PARTNER = 'partner';
    const INDIVIDUAL = 'individual';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }
}
