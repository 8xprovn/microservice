<?php

namespace Microservices\models\Dtms;

use Illuminate\Support\Arr;

class Result extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/dtms/result';
        $this->setToken($options['token'] ?? 'system');
    }
}
