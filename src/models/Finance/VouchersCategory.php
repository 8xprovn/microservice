<?php

namespace Microservices\models\Finance;

class VouchersCategory extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance/vouchers-category';
        
    }
}
