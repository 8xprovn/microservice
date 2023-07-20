<?php

namespace Microservices\models\Org;

use Illuminate\Support\Arr;

class Branchs extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/org/branch';
        $this->setToken($options['token'] ?? 'system');
    }
}
