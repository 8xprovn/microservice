<?php

namespace Microservices\models\Support;

use Illuminate\Support\Arr;

class Reminder extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/support/reminder';
        
    }

    public function completed($id)
    {
        $url = env('API_MICROSERVICE_URL_V2') . '/support/reminder/'.$id.'/completed';
        $result = $this->safePost($url, [], (string) $this->getToken());
        return $result === false ? false : $result;
    }
}
