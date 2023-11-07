<?php

namespace Microservices\models\Inventory;

class Transaction extends \Microservices\models\Model
{
    protected $_url;
//    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/inventory/transaction';
        $this->setToken($options['token'] ?? 'system');
    }
}
