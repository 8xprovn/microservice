<?php

namespace Microservices\models\Finance;

class Invoices extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance';
        if (!empty($options['token'])) {
            $this->setToken($options['token']);
        }
    }
    protected $prefix = 'invoices';
}
