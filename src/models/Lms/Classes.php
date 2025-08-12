<?php

namespace Microservices\models\Lms;

use Illuminate\Support\Arr;

class Classes extends \Microservices\models\Model
{
    protected $_url;
    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/lms/classes';
        
    }
}
