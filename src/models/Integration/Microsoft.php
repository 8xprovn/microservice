<?php

namespace Microservices\models\Integration;

use Illuminate\Support\Arr;

class Microsoft
{
    protected $_url;
    public function __construct($options = []) {

    }

    public function getAccessToken($params = [])
    {
        ///////// SETTING //////
        $tenantId = env('MICROSOFT_TENANT_ID');'';
        $clientId = env('MICROSOFT_CLIENT_ID');'';
        $clientSecret = env('MICROSOFT_CLIENT_SECRET');'';
        /////// CACHE KEY /////
        $cacheKey = 'microsoft_access_token_'.$clientId;
        ///// CACHE HERE /////
        $token = \Cache::get($cacheKey);
        if ($token) {
            return $token;
        }
        //// IF NOT ///////
        $url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/v2.0/token';

        $response = \Http::asForm()->post($url,[
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
        ]);
        $token = $response->json();
        if (!empty($token['access_token'])) {
            \Cache::put($cacheKey, $token['access_token'], 600);    
            return $token['access_token'];
        }
        return false;
    }
}
