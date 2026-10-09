<?php

namespace Microservices\models\Finance;

class WalletTransaction extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance/wallet-transaction';
        
    }

    public function deposit($param = [])
    {
        $url = $this->_url . "/deposit";
        $result = $this->safePost($url, $param, (string) $this->getToken());
        return $result === false ? [] : $result;
    }

    public function qr($param = [])
    {
        $url = $this->_url . "/qr/create";
        $result = $this->safeGet($url, $param, (string) $this->getToken());
        return $result === false ? [] : $result;
    }

    public function confirm_info($id, $param = [])
    {
        $url = $this->_url."/confirm_info/$id";
        $result = $this->safePost($url, $param, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
}
