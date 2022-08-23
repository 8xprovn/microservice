<?php
namespace Microservices;


class Pm
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/pm';
    }

    public function getTickets($params = array())
    {
        $whereArr = \Arr::only($params, ['ticket_id', 'created_type', 'created_id', 'assigned_id', 'status', 'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if($k == 'limit' || $k == 'offset') continue;
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
            if($k == 'limit' || $k == 'offset') continue;
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
            if($k == 'limit' || $k == 'offset') continue;
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
    
    //Create ticket
    public function createTicket($params = array())
    {
        $data = \Arr::only($params, ['name', 'branch_id', 'department_id', 'topic_id', 'description', 'created_type', 'created_id']);
        foreach($data as $k => $v){
            if(in_array($k, ['branch_id', 'department_id', 'topic_id', 'created_id'])){
                $data[$k] = (int)$v;
            }
        }
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/tickets', $data);

        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    
    //Create ticket
    public function createTicketProcess($params = array())
    {
        $data = \Arr::only($params, ['description', 'ticket_id', 'action']);
        foreach($data as $k => $v){
            if(in_array($k, ['ticket_id'])){
                $data[$k] = (int)$v;
            }
        }
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/ticket-processes', $data);

        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getSurveyParticipants($params = [])
    {
        $whereArr = \Arr::only($params, ['participant_id','survey_id','student_id','contact_id', 'status', 'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if($k == 'limit' || $k == 'offset') continue;
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
       
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/survey-participants',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
       
        \Log::error($response->body());
        return false;
    }

    public function getSurveyParticipantById($id)
    {
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/survey-participants/'.$id);

        if ($response->successful()) {
             return $response->json();
        }

        \Log::error($response->body());
        return false;
    }


    public function getSurveys($params = [])
    {
        $whereArr = \Arr::only($params, ['survey_id','class_id','category_id','status', 'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if($k == 'limit' || $k == 'offset') continue;
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
       
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/surveys',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getSurveyById($id)
    {
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/surveys/'.$id);

        if ($response->successful()) {
             return $response->json();
        }

        \Log::error($response->body());
        return false;
    }

    public function getNotes($params = array())
    {
        $whereArr = \Arr::only($params, ['note_id', 'relate_type', 'relate_id', 'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if($k == 'limit' || $k == 'offset') continue;
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

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/notes',['filter' => json_encode($newFilter)]);

        
        if ($response->successful()) {
            return $response->json();
        }
        dd($response->body());
        \Log::error($response->body());
        return false;
    }

    public function getNoteById($id)
    {
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/notes/'.$id);

        if ($response->successful()) {
             return $response->json();
        }

        \Log::error($response->body());
        return false;
    }
}
