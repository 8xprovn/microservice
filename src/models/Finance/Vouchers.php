<?php

namespace Microservices\models\Finance;

class Vouchers extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/finance/vouchers';
        
    }
    public function checkCode($params = [])
    {
        $url = $this->_url . '/code/check';
        $result = $this->safeGet($url, $params, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
}
