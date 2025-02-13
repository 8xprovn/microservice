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
        $response = \Http::acceptJson()->withToken($this->getToken())->get("{$this->_url}/holiday", $params);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error("{$this->_url}/holiday" . $response->body());
        return [];
    }
}
