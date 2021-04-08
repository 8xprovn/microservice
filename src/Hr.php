<?php
namespace Microservices;


class Hr
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/hr';
    }

    //EMPLOYEE

    public function getEmployees($params= array())
    {
        $whereArr = \Arr::only($params, ['employee_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['employee_id','department_id','branch_id','manager_id', 'first_name', 'last_name', 'birth_date', 'email' ,'phone']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getEmployeesIncludeRank($params= array()) {
        $whereArr = \Arr::only($params, ['employee_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'employeeTeacherRanks',
                    'scope' => [
                        "limit" => 1,
                        "order" => "created_time DESC",
                        //'fields'=> ['rank_id','employee_id ','level_id','created_by'],
                    ]
                ]
            ],
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getEmployeesIncludeDepartment($params = array()) {
        $whereArr = \Arr::only($params, ['employee_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'department',
                    // 'scope' => [
                    //     'fields'=> ['department_id','manager_id ','name','parent', 'code','mail_alias],
                    //     'where' => ['status' => 'active']
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


    public function getEmployeesIncludeShift($params = array()) {
        $whereArr = \Arr::only($params, ['employee_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'shift',
                    // 'scope' => [
                    //     'fields'=> ['shift_id','name','shift_data','days_of_week', 'type', 'brand_id','manday'],
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

    public function getEmployeesIncludeJob($params = array()) {
        $whereArr = \Arr::only($params, ['employee_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'jobtitle',
                    'scope' => [
                        'fields'=> ['job_title_id','name','code'],
                    ]
                ]
            ],
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getEmployeesIncludeSalary($params = array()) {
        $whereArr = \Arr::only($params, ['employee_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'employeeSalary',
                    // 'scope' => [
                    //     'fields'=> ['id','employee_id','start_date','basic_salary','salary','position_salary','actually_received','reason','attachment','created_by','approved_by','status'],
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


    public function getEmployeesIncludeActivities($params = array()) {
        $whereArr = \Arr::only($params, ['employee_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'employeeActivities',
                    // 'scope' => [
                    //     'fields'=> ['activity_id','employee_id','key','value_old','value_new','from_date','create_time','updated_time'],
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

    public function getRanksByEmployee($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees/'.$id.'/employee-teacher-ranks/');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getSalariesByEmployee($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees/'.$id.'/employee-salary/');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getActivitiesByEmployee($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees/'.$id.'/employee-activities/');
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
        $whereArr = \Arr::only($params, ['shift_id']);
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

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/setting-shifts',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['shift_id ','name','shift_data','manager_id', 'days_of_week', 'type', 'brand_id', 'manday' ]
            ])]);
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

    //TRACKING

    public function getTrackings($params = array())
    {
        $whereArr = \Arr::only($params, ['tracking_id', 'employee_id', 'tracking_type']);
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

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/trackings',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['tracking_id','employee_id','date_str','time_missing', 'ticket_id', 'branch_id', 'frequency', 'tracking_type' ,'status']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTrackingDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/trackings/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    //TICKET
    public function getTickets($params = array())
    {
        $whereArr = \Arr::only($params, ['ticket_id ', 'employee_id', 'type_id ']);
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
        $filter = array_merge($filter, ['status' => 'open']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tickets',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['ticket_id','type_id','employee_id','data', 'reason', 'status', 'created_time', 'number_days' ,'from_date' , 'reject_reason']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    

    public function getTicketsIncludeType($params = array()) {
        $whereArr = \Arr::only($params, ['ticket_id ', 'employee_id', 'type_id ']);
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

        $filter = array_merge($filter, ['status' => 'open']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tickets',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'ticketType',
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

    public function getTicketDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tickets/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTicketCategories($params = array())
    {
        $whereArr = \Arr::only($params, ['category_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-categories',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['ticket_id','type_id','employee_id','data', 'reason', 'status', 'created_time', 'number_days' ,'from_date' , 'reject_reason']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTicketCategoryDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-categories/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTicketTypesByCategory($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-categories/'.$id.'/ticket-types');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    //
    public function getTicketTypes($params = array())
    {
        $whereArr = \Arr::only($params, ['type_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-types',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['ticket_id','type_id','employee_id','data', 'reason', 'status', 'created_time', 'number_days' ,'from_date' , 'reject_reason']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTicketTypeDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-types/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTicketsByTicketType($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/ticket-types/'.$id.'/tickets');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    //
    public function getSchedule($params = array())
    {
    	$params = array_merge(['date' => date('Y-m-d')],$params);
    	$whereArr = \Arr::only($params, ['date', 'employee_id','working_time']);
    	$filter = [];
        foreach($whereArr as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
            	case 'working_time':
            		$filter['start_time'] = ['lt' => $v];
            		$filter['end_time'] = ['gt' => $v];
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
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/schedules',['filter' => json_encode(['where' => $filter])]);
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

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/notification-employees',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['tracking_id','employee_id','date_str','time_missing', 'ticket_id', 'branch_id', 'frequency', 'tracking_type' ,'status']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getNotificationsIncludeEmployees($params = array()) {
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

 
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/notification-employees',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'notificationToEmployees',
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

    public function getNotificationDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/notification-employees/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    //RANK
     public function getRanks($params = array())
     {
         $whereArr = \Arr::only($params, ['rank_id', 'employee_id']);
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
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employee-teacher-ranks',['filter' => json_encode([
             'where' => $filter,
             //'fields' => ['tracking_id','employee_id','date_str','time_missing', 'ticket_id', 'branch_id', 'frequency', 'tracking_type' ,'status']
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 

     public function getRankDetail($id)
     {
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employee-teacher-ranks/'.$id);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

    

     public function departments()
     {
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/departments');
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function employee($employeeId, $toDate)
     {
         $item['relation'] = "employeeSalaries";
         $item['scope'] = (object)[
             "offset" => 0,
             "limit" => 1,
             "order" => ["start_date DESC", "created_time DESC"],
             "where" => (object)[
                 "start_date" => (object)[
                     "lte" => $toDate
                 ],
                 "status" => "active"
             ]
         ];
         $query['include'][] = $item;
     
         $params['filter'] = json_encode($query);
         $params = array_filter($params);
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees/' . $employeeId . '?', http_build_query($params));
         if ($response->successful()) {
             
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function tracking($params=[])
     {
         $params = array_filter($params);
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/trackings/payroll?',http_build_query($params));
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function activity($params=[])
     {
         $params = array_filter($params);
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees/' . $params['employeeArr'] .'/employee-activities?',http_build_query($params));
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function department($params = [])
     {
         $params = array_filter($params);
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/departments/',http_build_query($params));
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function jobTitle($params = [])
     {
         $params = array_filter($params);
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'//employee-job-titles/',http_build_query($params));
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }


     public function getSalaryPolicy($params = [])
     {
         $params = array_filter($params);
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employee-salary-policies/',http_build_query($params));
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
}