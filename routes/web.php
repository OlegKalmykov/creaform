<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/database/reset', function () {
    \Artisan::call("migrate:refresh", ['--seed' => 'default']);

    return 'The database was successfully reset';
});

Route::get('/', function () {
    return view('home', ['users' => App\Api\Models\User::all()]);
});

/*Route::get('/delete/{email}', function ($email) {
    $users = \App\Api\Models\User::where('email', '=', $email)->get();

    if (empty($users->toArray())) {
        return 'there is no user with email - '.$email;
    }

    foreach ($users as $user) {
        echo $user->id;

        foreach ($user->roles() as $role) {
            $role->delete();
        }

        $user->company()->delete();

        $user->delete();

        return 'user with email - '.$email.' - deleted (userId: '.$user->id.')';
    }
});*/

//Auth::routes();
