<?php

namespace Microservices\models\Org;

use Illuminate\Support\Arr;

class Brands extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/org/branchs';
        $this->setToken($options['token'] ?? 'system');
    }
}
