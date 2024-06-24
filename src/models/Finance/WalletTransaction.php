<?php

namespace Microservices\models\Finance;

class WalletTransaction extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance/wallet-transaction';
        $this->setToken($options['token'] ?? 'system');
    }

    public function deposit($param = [])
    {
        $url = $this->_url . "/deposit";

        $response = \Http::acceptJson()->withToken($this->access_token)->post($url, $param);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
    }

    public function qr($param = [])
    {
        $url = $this->_url . "/qr/create";

        $response = \Http::acceptJson()->withToken($this->access_token)->get($url, $param);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
    }

    public function confirm_info($id, $param = [])
    {
        $url = $this->_url."/confirm_info/$id";

        $response = \Http::acceptJson()->withToken($this->access_token)->post($url, $param);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
    }
}
