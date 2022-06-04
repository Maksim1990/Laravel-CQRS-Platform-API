<?php
namespace App\Services;

use App\Exceptions\AuthenticationException;
use App\Exceptions\ItemNotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthManager
{
    public const TESTING_ENV = 'testing';
    public const ENVS_TO_DISABLE_TOKEN_EXPIRATION = [
        self::TESTING_ENV
    ];

    private const DATE_FORMAT = 'd-m-Y H:i:s';
    private const CACHED_DATA_FIELD_NAME = 'cachedData';
    private array $params;
    private string $token = '';

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @param  string $bearerToken
     * @return string|null
     * @throws AuthenticationException
     */
    public function getAuthToken(string $bearerToken): ?string
    {
        $jwtPayload = $this->parseJwtToken($bearerToken);

        //-- Preform token expiration checking if ENV is not 'testing'
        if (!in_array($this->params['env'], self::ENVS_TO_DISABLE_TOKEN_EXPIRATION)) {
            //-- Check if provided JWT token is not expired
            if (!$this->checkTokenExpirationDate($jwtPayload)) {
                throw new AuthenticationException('JWT Token is expired', Response::HTTP_UNAUTHORIZED);
            }
        }

        return $jwtPayload->user_id ?? null;
    }


    /**
     * @param  string $bearerToken
     * @return mixed|null
     * @throws AuthenticationException
     */
    public function parseJwtToken(string $bearerToken)
    {
        $arrToken = explode(' ', $bearerToken);
        if (count($arrToken) === 2) {
            $this->token = $arrToken[1];
            $tokenParts = explode(".", $this->token);
            if (count($tokenParts) > 2) {
                //$tokenHeader = base64_decode($tokenParts[0]);
                //$jwtHeader = json_decode($tokenHeader);
                $tokenPayload = base64_decode($tokenParts[1]);
                return json_decode($tokenPayload);
            }
        }
        throw new AuthenticationException('JWT Token has invalid format', Response::HTTP_UNAUTHORIZED);
    }

    public function registerUserInDbIfNotExist(string $userSystemId): User
    {
        $user = User::where('user_system_uuid', $userSystemId)->first();
        if ($user === null) {
            $user = User::create(
                [
                'name' => 'Admin',
                'user_system_uuid' => $userSystemId,
                'password' => Hash::make(config('system.system_user_pass')),
                ]
            );
        }

        return $user;
    }

    /**
     * @param  object $jwtPayload
     * @return bool
     */
    private function checkTokenExpirationDate(object $jwtPayload)
    {
        date_default_timezone_set(config('app.timezone'));
        $strDateNow = strtotime(date(self::DATE_FORMAT));
        $strDateIAT = isset($jwtPayload->iat) ? strtotime(date(self::DATE_FORMAT, $jwtPayload->iat)) : null;
        $strDateEXP = isset($jwtPayload->exp) ? strtotime(date(self::DATE_FORMAT, $jwtPayload->exp)) : null;

        return ((!is_null($strDateIAT) && !is_null($strDateEXP))
            && $strDateNow >= $strDateIAT && $strDateNow < $strDateEXP);
    }

    public function getAuthenticatedUserData(
        string $uuid,
        ?string $authToken = null,
        bool $forgetCache = false
    ): array {
        if ($authToken === null) {
            throw new AuthenticationException('Token was not provided');
        }

        if ($forgetCache) {
            Cache::forget($uuid);
        }

        if (Cache::has($uuid)) {
            return array_merge([self::CACHED_DATA_FIELD_NAME => true], Cache::get($uuid));
        }

        $this->getAuthToken($authToken);

        $graphQLClient = new GraphQLClient;
        $query = <<<'GRAPHQL'
                query GetUser($id: ID!) {
                  user(_id:$id){
                    _id
                    name
                    email
                    created_at
                    role
                    enabled
                    lastname
                    country
                    country_code
                    city
                    zip
                    address
                    phone
                    birthdate
                    bio
                    avatar
                    profile_background
                  }
                }
                GRAPHQL;

        $result = $graphQLClient->query(
            config('system.webmastery_main_api_url'),
            $query,
            ['id' => $uuid],
            $this->token
        );

        if ($result['data']['user'] === null) {
            throw new ItemNotFoundException(
                'User not found',
                Response::HTTP_NOT_FOUND
            );
        }

        Cache::remember(
            $uuid, config('system.app_cache_ttl'), function () use ($result) {
                return $result['data'];
            }
        );

        return array_merge([self::CACHED_DATA_FIELD_NAME => false], $result['data']);
    }
}
