<?php

namespace Microservices\models\Support;

class Likes extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/support/likes';
        
    }
    public function count_like($params)
    {
        $_url = env('API_MICROSERVICE_URL_V2') . "/support/likes/count";
        $response = \Http::acceptJson()->withToken($this->getToken())->get($_url, $params);
        if ($response->successful()) {
            return $response->json();
        }
        return 0;
    }

    public function change($data)
    {
        $_url = env('API_MICROSERVICE_URL_V2') . "/support/likes";
        $response = \Http::acceptJson()->withToken($this->getToken())->post($_url, $data);
        if ($response->successful()) {
            return $response->json();
        }
        return 0;
    }
}
