<?php

namespace Microservices\models\Hr;

use Illuminate\Support\Arr;

class Holiday extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr/holidays';
        $this->setToken($options['token'] ?? 'system');
    }
}
