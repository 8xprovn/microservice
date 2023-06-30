<?php
namespace Microservices;

class TestsV2
{
    protected $_url;
    protected $_hash_secret;
    public function __construct() {
        $this->_url = env('APP_URL').'/tests/api';
        $this->_hash_secret = env('TEST_HASH_SECRET');
    }

    public function getTestLogDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/test-logs/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
}
