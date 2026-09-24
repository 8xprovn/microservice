<?php

namespace Microservices\models\Hr;

use Illuminate\Support\Arr;

class Schedule extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/hr/schedule';
    }
    public function getHolidaytoEmployee($employee_id, $month, $year)
    {
        $params = ['employee_id' => $employee_id, 'month' => $month, 'year' => $year];
        $accessToken = $this->getToken();
        return $this->safeGet("{$this->_url}/holiday", $params ,$accessToken); 
    }
    public function getScheduleMe($userId, $key_cache, $params)
    {
        ////// GET FROM CACHE ////////
        $schedules = $this->cache()->getMe($userId, $key_cache);
        if ($schedules) {
            return $schedules;
        }
        ////// MISS CACHE //////////
        $data = $this->all($params);
        if (!empty($data)) {
            $this->cache()->putMe($userId, $key_cache, $data);
            return $data;
        }
        $this->cache()->flushMe($userId);
        return false;
    }
}
