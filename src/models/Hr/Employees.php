<?php

namespace Models\Hr;

use Illuminate\Support\Arr;

class Employees extends \Models\Model
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr';
    }
    protected $prefix = 'employees';
}
