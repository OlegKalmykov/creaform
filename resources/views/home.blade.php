@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Existing Users Table</div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <td>id</td>
                                <td>login</td>
                                <td>email</td>
                                <td>confirmed</td>
                                <td>confirm_code</td>
                                <td>token_id</td>
                                <td>expires_at</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$user['id']}}</td>
                                    <td>{{$user['login']}}</td>
                                    <td>{{$user['email']}}</td>
                                    <td>{{$user['confirmed']}}</td>
                                    <td>{{$user['confirm_code']}}</td>
                                    <td>{{$user['token_id']}}</td>
                                    <td>{{$user['expires_at']}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
