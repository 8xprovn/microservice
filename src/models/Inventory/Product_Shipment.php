<?php

namespace Microservices\models\Inventory;

class Product_Shipment extends \Microservices\models\Model
{
    protected $_url;
//    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/inventory/products_shipment';
        $this->setToken($options['token'] ?? 'system');
    }
}
