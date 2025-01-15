<?php

namespace Microservices\models\Test;

use Illuminate\Support\Arr;

class Question extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/tests/questions';
        
    }
}
