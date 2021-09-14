<?php
namespace Microservices;


class Pm
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/pm';
        $this->_local_url = env('APP_URL').'/pm';
    }

    public function getTickets($params = array())
    {
        $whereArr = \Arr::only($params, ['ticket_id', 'created_id', 'assigned_id', 'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    }
                    else if($v != 'limit' && $v != 'offset') {
                        $filter[$k] = ['eq' => $v];
                    }
                    break;
            }
        }

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tickets',['filter' => json_encode([
            'limit' => $limit,
            'offset' => $offset,
            'where' => $filter,
            //'fields' => ['contact_id','first_name','last_name','email', 'phone', 'gender', 'birthdate', 'organization' ,'address']
        ])]);

        
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTicketDetailById($id)
    {
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tickets/'.$id);

        if ($response->successful()) {
             return $response->json();
        }

        \Log::error($response->body());
        return false;
    }

    public function getTicketObjects($params = [])
    {
        $whereArr = \Arr::only($params, ['object_id', 'status', 'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    }
                    else if($v != 'limit' && $v != 'offset') {
                        $filter[$k] = ['eq' => $v];
                    }
                    break;
            }
        }

        $newFilter = [
            'limit' => $limit,
            'offset' => $offset
        ];

        if(count($filter) > 0) {
            $newFilter = [
                'limit' => $limit,
                'offset' => $offset,
                'where' => $filter,
            ];
        }
       
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-objects',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTicketObjectDetailById($id)
    {
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-objects/'.$id);

        if ($response->successful()) {
             return $response->json();
        }

        \Log::error($response->body());
        return false;
    }


    public function getTicketProcesses($params = array())
    {
        $whereArr = \Arr::only($params, ['process_id', 'ticket_id', 'parent_id', 'employee_id', 'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    }
                    else if($v != 'limit' && $v != 'offset') {
                        $filter[$k] = ['eq' => $v];
                    }
                    break;
            }
        }
        $newFilter = [
            'limit' => $limit,
            'offset' => $offset
        ];

        if(count($filter) > 0) {
            $newFilter = [
                'limit' => $limit,
                'offset' => $offset,
                'where' => $filter,
            ];
        }
       
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-processes',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTicketProcessDetailById($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-processes/'.$id);

        if ($response->successful()) {
             return $response->json();
        }

        \Log::error($response->body());
        return false;
    }

    public function createTicket($input)
    {
        $input = \Arr::only($input, ['name','priority','department_id','description']);
        try {
            $response = \Http::post($this->_local_url.'/api/ticket', $input);
            if ($response->successful()) {
                return $response->json();
            }
            \Log::error($response->body());
        }catch (\Throwable $e){
            $result =  ['status' => 'error', 'message' => $e->getMessage()];
            return $result;
        }
        return false;
    }

}