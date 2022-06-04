<?php

return [
    'system_user_token_name' => env('SYSTEM_USER_TOKEN_NAME', 'x-auth-user-id'),
    'system_user_pass' => env('SYSTEM_USER_TOKEN_PASS', 'pass'),
    'app_version' => env('APP_VERSION', '0.1'),
    'app_version_prefix' => env('APP_VERSION_PREFIX', 'v1'),
    'webmastery_main_api_url' => env('WEBMASTERY_MAIN_API_URL', 'http://wug_nginx/graphql'),
    'app_authorization_header_name' => 'Authorization',
    'app_cache_ttl' => 60 * 3, // 3 minutes


    'disable_test_auth_via_token' => env('APP_DISABLE_AUTH_TOKEN', false),
];
