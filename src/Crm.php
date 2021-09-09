<?php
namespace Microservices;

class Crm
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/crm';
    }

    //CONTACT

    public function getContacts($params = array())
    {
        $whereArr = \Arr::only($params, ['contact_id','manager_id','status','gender','email','phone', 'account_id', 'limit', 'offset']);
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
       
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/contacts',['filter' => json_encode($newFilter)]);
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
    public function getContactsIncludeNotifications($params = array()) {
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

    public function getAccounts($params =array())
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
    public function getLeads($params = array())
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
    public function getOpportunities($params = array())
    {
        $whereArr = \Arr::only($params, ['opportunity_id', 'email', 'phone', 'contact_id', 'account_id', 'limit', 'offset']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/opportunities',['filter' => json_encode([
            'limit' => $limit,
            'offset' => $offset,
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
    public function getNotifications($params = array())
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

    public function createNotification($notification = [], $arrContactId = []) {
       
        $notiParams = \Arr::only($notification, ['name','type','title','content','created_time','description','is_all','brand_id','file','type_sms','sub_type','employee_id','send_time','is_processed','attachment']);
        if(!empty($arrContactId)) {
            if(!is_array($arrContactId)) {
                $arrContactId = [$arrContactId];
            }
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/notification-contacts/to-contacts',[
                'notification' => $notiParams,
                'contact_id'=> $arrContactId 
            ]);
        } else {
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/notification-contacts', $notiParams);
        }
       

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getNotificationsIncludeContacts($params = array()) {
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

    //export

    //
    public function getExportOpportunities($params = [])
    {
        $whereArr = \Arr::only($params, ['branch_id','start_date','end_date','brand_id','source','contact_id','campaign_id','status','assigned_employee_id', 'account_id', 'is_closed']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;
        // > 92 ngay thi reject
        if (strtotime($whereArr['end_date']) - strtotime($whereArr['start_date']) > 7948800) {
            return \Response::json("Thời gian export không được quá 3 tháng", 422);
        }
        
        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                case 'start_date':
                    $filter['created_time'] = ['gte' => $v];
                    //$query->where('created_time', '>=', $v);
                    break;
                case 'end_date':
                    $filter['created_time'] = ['lte' => $v];
                    //$query->where('created_time', '<=', $v);
                    break;
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
            // 'limit' => $limit,
            // 'offset' => $offset,
            'where' => $filter,
            //'fields' => ['ticket_id','type_id','employee_id','data', 'reason', 'status', 'created_time', 'number_days' ,'from_date' , 'reject_reason']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getCountOpportunities($params = [])
    {
        $whereArr = \Arr::only($params, ['opportunity_id', 'email', 'phone', 'branch_id','start_date','end_date','brand_id','source','contact_id','campaign_id','status','assigned_employee_id','is_closed', 'account_id']);
        $filter = [];
        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                case 'start_date':
                    $filter['created_time'] = ['gte' => $v];
                    break;
                case 'end_date':
                    $filter['created_time'] = ['lte' => $v];
                    break;
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
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/opportunities/count',['where' => json_encode($filter)]);
        
        if ($response->successful()) {
            return $response->json()['count'];
        }
        \Log::error($response->body());
        return false;
    }

    public function getSumOpportunities($params = [])
    {
        $whereArr = \Arr::only($params, ['opportunity_id', 'email', 'phone', 'branch_id','start_date','end_date','brand_id','source','contact_id','campaign_id','status','assigned_employee_id','is_closed', 'account_id', 'limit', 'offset']);
        $filter = [];
        $limit =  isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;
       
        
        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                case 'start_date':
                    $filter['created_time'] = ['gte' => $v];
                    //$query->where('created_time', '>=', $v);
                    break;
                case 'end_date':
                    $filter['created_time'] = ['lte' => $v];
                    //$query->where('created_time', '<=', $v);
                    break;
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

    
        if(count($filter) > 0) {
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/opportunities',['filter' => json_encode([
                // 'limit' => $limit,
                // 'offset' => $offset,
                'where' => $filter,
                //'fields' => ['ticket_id','type_id','employee_id','data', 'reason', 'status', 'created_time', 'number_days' ,'from_date' , 'reject_reason']
                ])
            ]);
        } else {
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/opportunities');
        }
        
        if ($response->successful()) {
            $json = $response->json();
            $sum = 0;
            // $sum = array_reduce($json, function($carry, $item)
            // {
            //     return $carry + $item['invoice_amount'];
            // },0);

            foreach($json as $item) {
                if($item['invoice_amount']) {
                    $sum += $item['invoice_amount'];
                }
            }

            return $sum;
        }
        \Log::error($response->body());
        return false;
    }

    public function getContactRelations($params = [])
    {
        $whereArr = \Arr::only($params, ['first_contact_id', 'last_contact_id', 'type', 'limit', 'offset']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/contact-relations',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
        
        \Log::error($response->body());
        return false;
    }



}