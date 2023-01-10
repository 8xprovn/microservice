<?php

namespace Microservices\Models\CallCenter;

use Illuminate\Support\Arr;

class Calls extends \Models\Model
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/call-center';
        $this->prefix = "calls";
    }
}
