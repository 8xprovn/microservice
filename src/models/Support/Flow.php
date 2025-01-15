<?php

namespace Microservices\models\Support;

use Illuminate\Support\Arr;

class Flow extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/support/flow';
        
    }
}
