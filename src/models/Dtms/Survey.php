<?php

namespace Microservices\models\Dtms;

use Illuminate\Support\Arr;

class Survey extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/dtms/survey';
        $this->setToken($options['token'] ?? 'system');
    }
}
