<?php

namespace Microservices\Models\Hr;

use Illuminate\Support\Arr;

class Employees extends \Microservices\models\Model
{
    protected $_url;
    protected $prefix;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr';
        $this->prefix = 'employees';
    }
}
