<?php
namespace Microservices;


class Crm
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/crm';
    }

    //CONTACT

    public function getContacts($params)
    {
        $whereArr = \Arr::only($params, ['contact_id']);
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
        $filter = array_merge($filter, ['status' => 'active']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/contacts',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['contact_id','first_name','last_name','email', 'phone', 'gender', 'birthdate', 'organization' ,'address']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    public function findContact($phoneOrEmail) {
        if (filter_var($phoneOrEmail, FILTER_VALIDATE_EMAIL)) {
            $contacts = $this->getContacts(['email' => $phoneOrEmail]);
        }
        else {
            $phoneOrEmail = preg_replace('/^0/', '+84', $phoneOrEmail);
            $contacts = $this->getContacts(['phone' => $phoneOrEmail]);
        }

        return ($contacts) ? $contacts[0] : [];
    }
    public function getContactsIncludeNotifications($params) {
        $whereArr = \Arr::only($params, ['contact_id']);
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

 
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/contacts',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'notificationContacts',
                    // 'scope' => [
                    //     'fields'=> ['type_id','name','category_id ','status','max_days','max_times','template','level_approve'],
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

    public function getContactDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/contacts/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    //ACCOUNT

    public function getAccounts($params)
    {
        $whereArr = \Arr::only($params, ['account_id', 'employee_id']);
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

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/accounts',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['account_id ','name','employee_id','description', 'assigned_employee_id', 'account_type', 'phone', 'email' , 'address' , 'city' ,'district'  ]
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getAccountDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/accounts/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    
    //LEAD
    public function getLeads($params)
    {
        $whereArr = \Arr::only($params, ['lead_id', 'email', 'phone ']);
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

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/leads',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['lead_id','email','phone','data', 'facebook', 'subject', 'description', 'type' ,'source' , 'reject_reason']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

 
    public function getLeadDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/leads/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    
    //
    public function getOpportunities($params)
    {
        $whereArr = \Arr::only($params, ['opportunity_id', 'email', 'phone', 'contact_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/opportunities',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['ticket_id','type_id','employee_id','data', 'reason', 'status', 'created_time', 'number_days' ,'from_date' , 'reject_reason']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getOpportunitieDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/opportunities/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    //NOTIFICATION 
    public function getNotifications($params)
    {
        $whereArr = \Arr::only($params, ['notification_id']);
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

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/notification-contacts',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['notification_id','type','title','content', 'created_time', 'description', 'is_all', 'brand_id' ,'file']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getNotificationsIncludeContacts($params) {
        $whereArr = \Arr::only($params, ['notification_id']);
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

 
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/notification-contacts',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'crmContacts',
                    // 'scope' => [
                    //     'fields'=> ['contact_id','first_name','last_name ','email','phone','gender','birthdate','organization','address','assigned_employee_id'],
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

    public function getNotificationDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/notification-contacts/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
}