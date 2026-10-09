<?php

namespace Microservices\models\Notification;

use Illuminate\Support\Arr;

class App extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/notification/type/app';
        
    }

    public function updateReadStatus($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = env('API_MICROSERVICE_URL_V2') . '/notification/app/is_read/' . $id;
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? false : $result;
    }
}
