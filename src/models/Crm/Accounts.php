<?php

namespace Microservices\models\Crm;

use Illuminate\Support\Arr;

class Accounts extends \Microservices\models\Model
{
    protected $_url;
    //protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm/accounts';
        $this->setToken($options['token'] ?? 'system');
    }
}
