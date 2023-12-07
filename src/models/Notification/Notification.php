<?php
namespace Microservices\models\Notification;

use Illuminate\Support\Facades\Http;

class Notification
{
    protected $_url;
    public function __construct() {
        $this->_url = env('SERVICE_URL','').'/api';
    }
    public function send($params) {
        switch($params['type']) {
            default:
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/type/'.$params['type'], $params);
            if ($response->successful()) {
                return true;
            }
            return ['message' => $response->body()];
        }
    }
}
