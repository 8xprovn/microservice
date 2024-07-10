<?php

namespace Microservices\models\System;

use Illuminate\Support\Arr;

class Logs extends \Microservices\models\Model
{
    protected $_url;
    //protected $is_cache = 1;
    public function __construct($options = [])
    { 
        $this->_url = env('API_MICROSERVICE_URL_V2').'/core/logs';
        $this->setToken($options['token'] ?? 'system');
    }
}
