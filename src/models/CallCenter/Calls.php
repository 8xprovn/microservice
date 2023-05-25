<?php

namespace Microservices\models\CallCenter;

use Illuminate\Support\Arr;

class Calls extends \Microservices\models\Model
{
    protected $_url;
    protected $prefix = 'calls';
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/call-center';
    }
}

