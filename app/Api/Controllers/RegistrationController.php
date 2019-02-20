<?php

namespace App\Api\Controllers;

use App\Api\Models\User;
use App\Api\Services\OAuthLogin;
use App\Api\Services\Register\Register;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class RegistrationController extends Controller
{
    public function register(Request $request, Register $roleRegister)
    {
        $roleRegister->register($request->all());

        return response()->json([
            'message' => Lang::get('auth.registration_successful'),
        ]);
    }

    public function confirm($confirmWord, User $user, OAuthLogin $loginService)
    {
        $user->confirm($confirmWord);

        $response = $loginService->attemptLoginAfterConfirm($user->getEmailForPasswordReset());

        return response()->json(array_merge(
            ['message' => Lang::get('auth.confirm_successful')],
            $response
        ));
    }
}
