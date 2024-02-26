<?php

namespace Microservices\models\Finance;

class Vouchers extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance/vouchers';
        $this->setToken($options['token'] ?? 'system');
    }
}
