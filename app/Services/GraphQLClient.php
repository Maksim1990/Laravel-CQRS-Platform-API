<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;

class GraphQLClient
{
    function query(string $endpoint, string $query, array $variables = [], ?string $token = null): array
    {
        $httpClient = Http::withHeaders(
            [
                'Content-Type' => 'application/json',
            ]
        );

        if (null !== $token) {
            $httpClient->withToken($token);
        }

        return $httpClient->post($endpoint, ['query' => $query, 'variables' => $variables])->json();
    }
}
