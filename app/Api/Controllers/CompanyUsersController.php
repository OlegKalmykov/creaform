<?php

namespace App\Api\Controllers;

use App\Api\Services\UserImport;
use App\Api\Services\UserExport;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class CompanyUsersController extends Controller
{
    public function importUsers(Request $request, UserImport $userImport, UserExport $userExport)
    {
        $file = $request->file('file');

        if (is_null($file)) {
            throw new ApiException(Lang::get('exceptions.file_not_uploaded'), 400);
        }

        $users = $userImport->run($file->getRealPath());

        return $userExport->run($users);
    }
}
