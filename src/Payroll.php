<?php
namespace Microservices;


class Payroll
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/payroll';
    }

    public function getAllBenefitWithActive()
    {
        $filter = ['status' => 'active'];

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/benefits/',['filter' => json_encode(['where' => $filter])]);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    //TEMPLATE

     public function getTemplates($params = array())
     {
         $whereArr = \Arr::only($params, ['template_id']);
         $filter = [];
         foreach($whereArr as $k => $v){
             if (is_null($v)) continue;
             switch ($k) {
                 default:
                     if (is_array($v)) {
                         $filter[$k] = ['inq' => $v];
                     }
                     else {
                         $filter[$k] = ['eq' => $v];
                     }
                     break;
             }
         }
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/templates',['filter' => json_encode([
             'where' => $filter,
             //'fields' => ['shift_id ','name','shift_data','manager_id', 'days_of_week', 'type', 'brand_id', 'manday' ]
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function getTemplateDetail($id)
     {
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/templates/'.$id);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     //SYSTEM - VARIABLE

     public function getSystemVariables($params = array())
     {
         $whereArr = \Arr::only($params, ['variable_id']);
         $filter = [];
         foreach($whereArr as $k => $v){
             if (is_null($v)) continue;
             switch ($k) {
                 default:
                     if (is_array($v)) {
                         $filter[$k] = ['inq' => $v];
                     }
                     else {
                         $filter[$k] = ['eq' => $v];
                     }
                     break;
             }
         }
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/system-variables',['filter' => json_encode([
             'where' => $filter,
             //'fields' => ['shift_id ','name','shift_data','manager_id', 'days_of_week', 'type', 'brand_id', 'manday' ]
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function getSystemVariableDetail($id)
     {
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/system-variables/'.$id);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }


      //SETTING

      public function getSettings($params = array())
      {
          $whereArr = \Arr::only($params, ['setting_id']);
          $filter = [];
          foreach($whereArr as $k => $v){
              if (is_null($v)) continue;
              switch ($k) {
                  default:
                      if (is_array($v)) {
                          $filter[$k] = ['inq' => $v];
                      }
                      else {
                          $filter[$k] = ['eq' => $v];
                      }
                      break;
              }
          }
  
          $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/settings',['filter' => json_encode([
              'where' => $filter,
              //'fields' => ['shift_id ','name','shift_data','manager_id', 'days_of_week', 'type', 'brand_id', 'manday' ]
              ])]);
          if ($response->successful()) {
              return $response->json();
          }
          \Log::error($response->body());
          return false;
      }
  
      public function getSettingDetail($id)
      {
          $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/settings/'.$id);
          if ($response->successful()) {
              return $response->json();
          }
          \Log::error($response->body());
          return false;
      }

      //EMPLOYEE

      public function getEmployees($params = array())
      {
          $whereArr = \Arr::only($params, ['id','employee_id','department_id','manager_id']);
          $filter = [];
          foreach($whereArr as $k => $v){
              if (is_null($v)) continue;
              switch ($k) {
                  default:
                      if (is_array($v)) {
                          $filter[$k] = ['inq' => $v];
                      }
                      else {
                          $filter[$k] = ['eq' => $v];
                      }
                      break;
              }
          }
  
          $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode([
              'where' => $filter,
              //'fields' => ['shift_id ','name','shift_data','manager_id', 'days_of_week', 'type', 'brand_id', 'manday' ]
              ])]);
          if ($response->successful()) {
              return $response->json();
          }
          \Log::error($response->body());
          return false;
      }


      public function getEmployeesIncludeFomulas($params = array()) {
        $whereArr = \Arr::only($params, ['id', 'employee_id','department_id']);
        $filter = [];
        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    }
                    else {
                        $filter[$k] = ['eq' => $v];
                    }
                    break;
            }
        }

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'payrollFormulas',
                    // 'scope' => [
                    //     'fields'=> ['formula_id','name','created_by','description','created_time','updated_time'],
                    // ]
                ]
            ],
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getFomulasByEmployee($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees/'.$id.'/formulas/');
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

      //FORMULAS
      
      public function getFormulas($params = array())
      {
          $whereArr = \Arr::only($params, ['formula_id']);
          $filter = [];
          foreach($whereArr as $k => $v){
              if (is_null($v)) continue;
              switch ($k) {
                  default:
                      if (is_array($v)) {
                          $filter[$k] = ['inq' => $v];
                      }
                      else {
                          $filter[$k] = ['eq' => $v];
                      }
                      break;
              }
          }
  
          $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/formulas',['filter' => json_encode([
              'where' => $filter,
              //'fields' => ['shift_id ','name','shift_data','manager_id', 'days_of_week', 'type', 'brand_id', 'manday' ]
              ])]);
          if ($response->successful()) {
              return $response->json();
          }
          \Log::error($response->body());
          return false;
      }

      public function getFomulaDetailsByFomula($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/formulas/'.$id.'/formula-details/');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
  
      public function getFormulaDetail($id)
      {
          $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/formulas/'.$id);
          if ($response->successful()) {
              return $response->json();
          }
          \Log::error($response->body());
          return false;
      }


      public function getFormulasIncludeDetails($params = array()) {
        $whereArr = \Arr::only($params, ['id', 'employee_id','department_id']);
        $filter = [];
        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    }
                    else {
                        $filter[$k] = ['eq' => $v];
                    }
                    break;
            }
        }

     
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/formulas',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'payrollFormulaDetails',
                    // 'scope' => [
                    //     'fields'=> ['detail_id','code_name','display_name','define','type','type_setting','formula_id','ordering'],
                    // ]
                ]
            ],
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getPolicyGroup()
    {

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/policy-groups/');

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getPolicyWithStatusActive()
    {
        $filter = ['status' => 'active'];
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/policies/', ['filter' => json_encode(['where' => $filter, 'limit' => 100])]);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

}