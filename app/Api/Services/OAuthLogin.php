<?php

namespace App\Api\Services;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

class OAuthLogin
{
    private $apiConsumer;
    private $auth;
    private $db;
    private $error;

    public function __construct(Application $app)
    {
        $this->apiConsumer = $app->make('apiconsumer');
        $this->auth = $app->make('auth');
        $this->db = $app->make('db');
    }

    /**
     * @param $email
     * @param $password
     * @param $accessToken
     * @return array|false
     */
    public function attemptLogin($email, $password, $accessToken)
    {
        $user = $this->db
            ->table('users')
            ->select('users.password', 'users.id', 'users.email', 'users.confirmed', 'oauth_access_tokens.expires_at')
            ->selectRaw('oauth_access_tokens.id as accessToken')
            ->leftJoin('oauth_access_tokens', 'users.id', '=', 'oauth_access_tokens.user_id')
            ->where('email', trim($email))
            ->orWhere('login', trim($email))
            ->first();

        if (is_null($user)) {
            $this->error = ['error' => Lang::get('auth.failed')];

            return false;
        }

        if (!$user->confirmed) {
            $this->error = ['error' => Lang::get('auth.unconfirmed')];

            return false;
        }

        if ($accessToken && $user->accessToken && strtotime($user->expires_at) > time()) {
            $this->error = ['error' => Lang::get('auth.already_authorized')];

            return false;
        }

        return $this->proxy('password', [
            'username' => !is_null($user->email) ? $user->email : $email,
            'password' => $password
        ]);
    }

    /**
     * Attempt to login after user register confirm
     * @param $email
     * @return array|false
     */
    public function attemptLoginAfterConfirm($email)
    {
        return $this->proxy('non_password', [
            'username' => $email
        ]);
    }

    /**
     * Attempt to refresh the access token used a refresh token
     * @param $refreshToken
     * @return array
     */
    public function attemptRefresh($refreshToken)
    {
        return $this->proxy('refresh_token', [
            'refresh_token' => $refreshToken
        ]);
    }

    /**
     * Proxy a request to the OAuth server.
     *
     * @param string $grantType what type of grant type should be proxied
     * @param array $data the data to send to the server
     * @return array|false
     */
    public function proxy($grantType, array $data = [])
    {
        $data = array_merge($data, [
            'client_id'     => env('PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSWORD_CLIENT_SECRET'),
            'grant_type'    => $grantType
        ]);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $this->apiConsumer->post('/oauth/token', $data);

        if (!$response->isSuccessful()) {
            $this->error = ['error' => Lang::get('auth.failed')];

            return false;
        }

        $data = json_decode($response->getContent());

        return [
            'access_token'  => $data->access_token,
            'refresh_token' => $data->refresh_token
        ];
    }

    /**
     * Logs out the user. Delete access token and refresh token.
     */
    public function logout()
    {
        /** @var \Laravel\Passport\Token $accessToken */
        $accessToken = $this->auth->user()->token();

        $this->db
            ->table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->delete();

        $accessToken->forceDelete();
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}