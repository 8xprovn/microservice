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
        $result = $this->safeGet($_url, $params, (string) $this->getToken());
        return $result === false ? 0 : $result;
    }

    public function change($data)
    {
        $_url = env('API_MICROSERVICE_URL_V2') . "/support/likes";
        $result = $this->safePost($_url, $data, (string) $this->getToken());
        return $result === false ? 0 : $result;
    }
}
