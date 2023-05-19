<?php

namespace Microservices\models\Finance;

class InvoicesDetail extends \Microservices\models\Model
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance';
    }
    protected $prefix = 'invoices-detail';
}