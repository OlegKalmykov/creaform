<?php

use Illuminate\Support\Facades\Route;

Route::post('/database/reset', function () {
    \Artisan::call("migrate:refresh", ['--seed' => 'default']);

    return response()->json('The database was successfully reset');
});


// user register
Route::post('/register', 'RegistrationController@register');
Route::post('/register/confirm/{confirm_code}', 'RegistrationController@confirm');


// user auth
Route::post('/auth', 'AuthController@login');
Route::get('/refresh', 'AuthController@refresh');
Route::middleware('auth:api')->post('/logout', 'AuthController@logout');


// user password reset
Route::post('/password/email', 'PasswordResetController@sendResetLinkEmail');
Route::post('/password/reset', 'PasswordResetController@reset');


// user profile (needed?)
Route::middleware('auth:api')->get('/profile', 'ProfileController@getProfile');


// company structure
Route::middleware(['auth:api', 'role:company'])->get('/company/structure', 'DepartmentController@getStructure');

Route::middleware(['auth:api', 'role:company'])->post('/company/structure/department', 'DepartmentController@addDepartment');
Route::middleware(['auth:api', 'role:company'])->delete('/company/structure/department/{id}', 'DepartmentController@deleteDepartment');
Route::middleware(['auth:api', 'role:company'])->put('/company/structure/department/{id}', 'DepartmentController@renameDepartment');

Route::middleware(['auth:api', 'role:company'])->post('/company/structure/position', 'PositionController@addPosition');
Route::middleware(['auth:api', 'role:company'])->delete('/company/structure/position/{id}', 'PositionController@deletePosition');
Route::middleware(['auth:api', 'role:company'])->put('/company/structure/position/{id}', 'PositionController@renamePosition');


// user import
Route::middleware(['auth:api', 'role:company'])->post('/users/import', 'CompanyUsersController@importUsers');
