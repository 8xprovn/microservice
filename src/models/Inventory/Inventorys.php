<?php

namespace Microservices\models\Inventory;

class Inventorys extends \Microservices\models\Model
{
    protected $_url;
//    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/inventory/inventory-products';
        $this->setToken($options['token'] ?? 'system');
    }
}
