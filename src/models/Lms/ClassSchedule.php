<?php

namespace Microservices\models\Lms;

use Illuminate\Support\Arr;

class ClassSchedule extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/lms/class-schedules';
        
    }

    public function semesterTest($id, $params)
    {
        $url = $this->_url . '/' . $id . '/semester-test';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
    public function cancelSemesterTest($id, $params)
    {
        $url = $this->_url . '/' . $id . '/cancel-semester-test';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
    public function updateBySchedule($id, $params)
    {
        $url = $this->_url . '/' . $id . '/update_by_schedule';
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
}
