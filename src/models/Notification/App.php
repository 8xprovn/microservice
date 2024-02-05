<?php

namespace Microservices\models\Notification;

use Illuminate\Support\Arr;

class App extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/notification/type/app';
        $this->setToken($options['token'] ?? 'system');
    }
}
