<?php

namespace Microservices\models\Lms;

use Illuminate\Support\Arr;

class StudentHighScore extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/lms/student/highscore';
        
    }

    public function storeTicket($id,array $params)
    {
        $url = env('API_MICROSERVICE_URL_V2').'/lms/student/highscore' . "/$id/log";
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
}
