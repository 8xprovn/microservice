<?php

namespace Microservices\models\Lms;

use Illuminate\Support\Arr;

class Course extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/course/classes';
        $this->setToken($options['token'] ?? 'system');
    }
}
