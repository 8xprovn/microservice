<?php

namespace Microservices\models\Support;

use Illuminate\Support\Arr;

class Note extends \Microservices\models\Model
{
    protected $_url;
    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/support/notes';
        
    }
}
