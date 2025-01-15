<?php

namespace Microservices\models\Hr;

use Illuminate\Support\Arr;

class Employees extends \Microservices\models\Model
{
    protected $_url;
    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr/employees';
        
    }
    
}
