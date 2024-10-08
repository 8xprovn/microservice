<?php

namespace Microservices\models\Asset;

class Broadcast extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/asset/broadcast';
        $this->setToken($options['token'] ?? 'system');
    }
}

