<?php

namespace App\Api\Controllers;

use App\Api\Services\OAuthLogin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;

class AuthController extends Controller
{
    private $loginService;
    private $response;

    public function __construct(OAuthLogin $loginService, ResponseFactory $response)
    {
        $this->loginService = $loginService;
        $this->response = $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $email = $request->get('email') ? $request->get('email') : $request->get('login');
        $password = $request->get('password');
        $accessToken = $request->bearerToken();

        $response = $this->loginService->attemptLogin($email, $password, $accessToken);

        if (!$response) {
            return $this->response->json($this->loginService->getError(), 400);
        }

        return $this->response->json($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $refreshToken = $request->header('Refresh');

        $response = $this->loginService->attemptRefresh($refreshToken);

        if (!$response) {
            return $this->response->json($this->loginService->getError(), 400);
        }

        return $this->response->json($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->loginService->logout();

        return $this->response->json(null, 204);
    }
}
