<?php

namespace Microservices\models\Hr;

use Illuminate\Support\Arr;

class Document extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/hr/documents';
        $this->setToken($options['token'] ?? 'system');
    }

    public function getEmployeeId($params = [], $options = [])
    {
        $filter = [];
        foreach($params as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    $filter[$k] = $v;
                    break;
            }
        }
        $q = $options;
        $q['filter'] = $filter;
        $response = \Http::acceptJson()->withToken($this->access_token)->get($this->_url, $q);
        if ($response->successful()) {
            $employee_id = [];
            $document = collect($response->json())->first();
            if(empty($document)){
                return [];
            }
            if (!empty($document['related_type'])) {
                $document['related'][] = [
                    'type' => 'type',
                    'id' => $document['related_type']
                ];
            }
            if (empty($document['related'])) {
                return [];
            }
            foreach($document['related'] as $relate){
                if(empty($relate['type'])){
                    continue;
                }
                switch($relate['type']){
                    case "employee":
                        $employee_id = array_merge($employee_id, $relate['id']);
                        break;
                    default: 
                        if ($relate['type'] == 'department') {
                            $relate['type'] = 'rel_department_id';
                        }
                        if ($relate['type'] == 'branch') {
                            $relate['type'] = 'branch_id';
                        }
                        if ($relate['type'] == 'brand') {
                            $relate['type'] = 'brand_id';
                        }
                        $filterEmployee[$relate['type']] = $relate['id'];
                        break;
                }
            }
            if (!empty($filterEmployee)) {
                $url = env('API_MICROSERVICE_URL_V2').'/hr/employees';
                $filterEmployee['status'] = 'active';
                $employees = \Http::acceptJson()->withToken($this->access_token)->get($url, ['filter' => $filterEmployee]);
                if ($employees->successful()) {
                    $employee_id = array_merge($employee_id, collect($employees->json())->pluck('_id')->unique()->values()->all());
                }
            }
            return array_unique($employee_id);
        } 
        \Log::error($this->_url . $response->body());
        return [];
    }
}
