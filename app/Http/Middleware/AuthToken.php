<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthenticationException;
use App\Services\AuthManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthToken
{
    private AuthManager $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next)
    {
        $this->processUserAuthentication($request);

        return $next($request);
    }

    private function throwAuthException(string $message)
    {
        throw new AuthenticationException($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param  Request $request
     * @throws AuthenticationException
     */
    private function processUserAuthentication(Request $request): void
    {
        if (!config('system.disable_test_auth_via_token')) {
            $authHeader = $request->header(config('system.app_authorization_header_name'));

            if (is_null($authHeader)) {
                $this->throwAuthException('Authorization token must be provided');
            }

            if (($authUserSystemId = $this->authManager->getAuthToken($authHeader)) !== null) {
                $request->headers->set(config('system.system_user_token_name'), $authUserSystemId);
                $user = $this->authManager->registerUserInDbIfNotExist($authUserSystemId);
                if (!Auth::attempt(
                    [
                    'user_system_uuid' => $user->user_system_uuid,
                    'password' => config('system.system_user_pass')
                    ]
                )
                ) {
                    $this->throwAuthException('Can\'t authenticate user');
                }
            }
        }
    }
}
