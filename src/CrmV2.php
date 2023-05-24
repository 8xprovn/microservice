<?php
namespace Microservices;

class CrmV2
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm';
    }

    //EMPLOYEE
    public function getEmployees($params= array())
    {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',$params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
}
