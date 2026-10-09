<?php
namespace Microservices\models\Notification;

use Microservices\Traits\SafeHttpClient;

class Notification
{
    use SafeHttpClient;

    protected $_url;
    public function __construct() {
        $this->_url = env('SERVICE_URL','').'/api';
    }
    public function send($params) {
        switch($params['type']) {
            default:
            $result = $this->safePost($this->_url.'/type/'.$params['type'], $params, (string) env('API_MICROSERVICE_TOKEN',''));
            if ($result === false) {
                return ['message' => ''];
            }
            return true;
        }
    }
}
