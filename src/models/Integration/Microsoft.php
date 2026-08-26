<?php

namespace Microservices\models\Integration;

use Illuminate\Support\Arr;

class Microsoft
{
    protected $_url;
    public function __construct($options = []) {

    }

    public function getAccessToken($delegated = false, $forceRefresh = false)
    {
        $tenantId = env('MICROSOFT_TENANT_ID');
        $clientId = env('MICROSOFT_CLIENT_ID');
        $clientSecret = env('MICROSOFT_CLIENT_SECRET');
        $cacheKey = $delegated
            ? 'microsoft_delegated_token_'.$clientId
            : 'microsoft_access_token_'.$clientId;
        $cacheSeconds = $delegated ? 3000 : 600;

        if (!$forceRefresh) {
            $token = \Cache::get($cacheKey);
            if ($token) {
                return $token;
            }
        }

        $params = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ];
        if ($delegated) {
            $params = array_merge($params, [
                'grant_type' => 'password',
                'username' => env('MICROSOFT_SENDER_UPN'),
                'password' => env('MICROSOFT_SENDER_PASSWORD'),
            ]);
        }

        $response = \Http::asForm()->post(
            'https://login.microsoftonline.com/' . $tenantId . '/oauth2/v2.0/token',
            $params
        );
        $token = $response->json();
        if (!empty($token['access_token'])) {
            \Cache::put($cacheKey, $token['access_token'], $cacheSeconds);
            return $token['access_token'];
        }
        return false;
    }
}
