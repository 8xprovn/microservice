<?php

namespace Microservices\models\Finance;

class Invoices extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance';
        $this->setToken($options['token'] ?? 'system');
    }
    protected $prefix = 'invoices';
    protected $service = 'erp_finance_backend_v2';
    protected $table = 'invoices';
}
