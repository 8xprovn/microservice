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
    
    public function createNotification($notification = [], $arrEmployeeId = []) {
        $notiParams = \Arr::only($notification, ['name','type','title','content','created_time','description','is_all','brand_id','file','type_sms','sub_type','employee_id','send_time','is_processed','attachment']);
        $params = [
            'notification' => $notiParams
        ];
        if(!empty($arrEmployeeId)) {
            if(!is_array($arrEmployeeId)) {
                $arrEmployeeId = [$arrEmployeeId];
            }
            $params['employee_id'] = $arrEmployeeId; 
        }
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/notifications', $params);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getSchedule($params = [])
     {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/schedule',$params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
     }
    
}
