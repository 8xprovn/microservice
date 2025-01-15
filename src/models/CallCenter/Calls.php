<?php

namespace Microservices\models\CallCenter;

use Illuminate\Support\Arr;

class Calls extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/call-center/calls';
        
    }
}

