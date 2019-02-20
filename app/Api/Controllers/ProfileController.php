<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(Request $request)
    {
        /** @var \App\Api\Models\User $user */
        $user = $request->user();
        $data = $user->getProfile();

        return response()->json($data);
    }
}
