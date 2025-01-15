<?php

namespace Microservices\models\Org;

use Illuminate\Support\Arr;

class Brands extends \Microservices\models\Model
{
    protected $_url;
    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/org/brand';
        
    }
}
