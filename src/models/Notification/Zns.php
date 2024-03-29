<?php

namespace Microservices\models\Notification;

use Illuminate\Support\Arr;

class Zns extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/notification/type/zns';
        $this->setToken($options['token'] ?? 'system');
    }
}
