<?php

namespace App\Http\Controllers;

use App\Services\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SystemController extends Controller
{
    public function test(Request $request)
    {
        dd(Auth::user()->name);
        ///  dd($request->headers->get(config('system.system_user_token_name')));
    }

    public function redis()
    {
        Cache::put('wug-api', 'Value from Redis Webmastery School', 10);
        $value = Cache::get('wug-api');
        dd($value);
    }

    public function redisKey(string $key)
    {
        dd(Cache::get($key));
    }

    /**
     * @OA\Get(
     *      path="/auth/data/{userUuid}",
     *      operationId="getAuthUserDataById",
     *      tags={"System"},
     *      summary="Get current application version",
     *      description="Returns application version",
     * @OA\Parameter(
     *          name="userUuid",
     *          in="path",
     *          description="ID of user to be retrieved",
     *          required=true,
     * @OA\Schema(
     *         type="string"
     *        )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successfully received application version"
     *       ),
     * @OA\Response(response=404, description="User not found"),
     *      security={
     *         {"bearerAuth": {}}
     *      }
     * )
     * @param                     Request     $request
     * @param                     string      $userUuid
     * @param                     AuthManager $authManager
     * @return                    \Illuminate\Http\JsonResponse
     * @throws                    \App\Exceptions\ItemNotFoundException
     */
    public function getAuthUserData(Request $request, string $userUuid, AuthManager $authManager)
    {
        return response()->json(
            $authManager->getAuthenticatedUserData(
                $userUuid,
                $request->header(config('system.app_authorization_header_name')),
                false
            )
        );
    }

    /**
     * @OA\Get(
     *      path="/version",
     *      operationId="getCourseById",
     *      tags={"System"},
     *      summary="Get current application version",
     *      description="Returns application version",
     * @OA\Response(
     *          response=200,
     *          description="Successfully received application version",
     * @OA\JsonContent(ref="#/components/schemas/System")
     *       )
     * )
     */
    public function version()
    {
        return response()->json(['version' => config('system.app_version')]);
    }
}
