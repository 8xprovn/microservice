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
        $response = \Http::acceptJson()->withToken($this->getToken())->get($url, $params);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
    }
}
