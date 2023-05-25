<?php

namespace Microservices\models\Hr;

use Illuminate\Support\Arr;

class Employees extends \Microservices\models\Model
{
    protected $_url;
    protected $prefix = 'employees';
    public function __construct($options) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr';
        if (!empty($options['token'])) {
            $this->setToken($options['token']);
        }
    }
}
