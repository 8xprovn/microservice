<?php

namespace Microservices\models\Support;

class Comments extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/support/comment';
        $this->setToken($options['token'] ?? 'system');
    }
    public function count($params)
    {
        $_url = env('API_MICROSERVICE_URL_V2') . '/support/comment/count';
        $response = \Http::acceptJson()->withToken($this->access_token)->get($_url, $params);
        if ($response->successful()) {
            return $response->json();
        } 
        return 0;
    }
}
