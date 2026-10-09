<?php

namespace Microservices\models\Finance;

class PaymentRequest extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance/payments-request';
        
    }

    public function send($id, $param = [])
    {
        $url = $this->_url . "/$id/send";
        $result = $this->safePost($url, $param, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
}
