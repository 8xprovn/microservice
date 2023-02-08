<?php

namespace Microservices\Models\Hr;

use Illuminate\Support\Arr;

class Employees extends \Microservices\models\ModelV2
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr';
    }
    protected $prefix = 'employees';
}
