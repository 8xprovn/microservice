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

        $response = \Http::acceptJson()->withToken($this->getToken())->post($url, $param);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
    }
}
