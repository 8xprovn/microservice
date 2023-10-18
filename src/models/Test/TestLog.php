<?php

namespace Microservices\models\Test;

use Illuminate\Support\Arr;

class TestLog extends \Microservices\models\Model
{
    protected $_url;
    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/tests/test-logs';
        $this->setToken($options['token'] ?? 'system');
    }
}
