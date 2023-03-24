<?php
namespace Microservices;

class HrV2
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/hr';
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

    public function getEmployeeDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees/'.$id);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    //SETTING SHIFT
    public function getSettingShifts($params = array())
    {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/setting-shifts',$params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getSettingShiftDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/setting-shifts/'.$id);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
  
     public function getDepartments($params = [])
     {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/departments',$params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
     }

     public function getDepartmentDetail($id)
     {
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/departments/'.$id);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
     }
}
