<?php

namespace App\Api\Models;

use App\Api\Notifications\ConfirmUserRegister as ConfirmUserRegisterNotification;
use App\Api\Notifications\ResetPassword as ResetPasswordNotification;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 'email', 'password', 'name', 'surname', 'patronymic', 'confirmed', 'confirm_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, $this->id));
    }

    /**
     * Send the register confirm notification.
     *
     * @param $token
     */
    public function sendConfirmRegisterNotification($token)
    {
        $this->notify(new ConfirmUserRegisterNotification($token));
    }

    /**
     * Confirm user by confirm code from mail
     *
     * @param $confirmWord
     * @throws ApiException
     */
    public function confirm($confirmWord)
    {
        $builder = $this->newQuery()->where('confirm_code', '=', $confirmWord);
        $user = $builder->get();

        if (empty($user->toArray())) {
            throw new ApiException(Lang::get('exceptions.wrong_confirm_code'), 400);
        }

        $builder->update(['confirm_code' => '', 'confirmed' => 1]);

        $this->fill($user->first()->toArray());
    }

    /**
     * Auth Method for oauth-lib
     * @param $username
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function findForPassport($username)
    {
        $validator = Validator::make(['email' => $username], ['email' => 'email']);

        if (!$validator->passes()) {
            return $this->where('login', $username)->first();
        }

        return $this->where('email', $username)->first();
    }

    /**
     * @param $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        return in_array($roleName, $this->roles()->pluck('name')->toArray());
    }

    /**
     * @param null $fictiveId
     * @return string
     */
    public function getCode($fictiveId = null)
    {
        if (!$fictiveId) {
            $fictiveId = $this->id;
        }

        $number = sprintf("%06d", $fictiveId);

        $leftHalf = substr($number, 0, 3);
        $rightHalf = substr($number, 3, 3);

        return $leftHalf.'-'.$rightHalf;
    }

    /**
     * @return array
     */
    public function getProfile()
    {
        $user = array_filter($this->attributesToArray(), function ($key) {
            return in_array($key, ['name', 'company_id', 'confirmed']);
        }, ARRAY_FILTER_USE_KEY);

        $user['roles'] = $this->roles()->get(['id', 'name'])->toArray();

        return $user;
    }
}
