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
        $whereArr = \Arr::only($params, ['employee_id','manager_id','branch_id','department_id', 'type', 'limit', 'offset']);
        $filter = [];
        foreach($whereArr as $k => $v){
            if (is_null($v) || $k == 'limit' || $k == 'offset') continue;
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
        $filter = array_merge($filter, ['status' => ['eq' => 'active']]);

        $newFilter = [
            'where' => $filter,
        ];

        if(!empty($whereArr['limit'])){
            $newFilter['limit'] = $whereArr['limit'];
        }

        if(!empty($whereArr['offset'])){
            $newFilter['offset'] = $whereArr['offset'];
        }

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',['filter' => json_encode($newFilter)]);
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
        if (is_array($id)) {
            if (empty($id)) {
                return [];
            }
            $fn = function (\Illuminate\Http\Client\Pool $pool) use ($id) {
                $limit = 200;
                $countId = count($id);
                $count = ceil($countId / $limit);
                for ($i = 0; $i < $count; $i ++) {
                    $arrId = array_slice($id, $i*$limit, ($i+1)*$limit);
                    $newFilter = ['filter' => json_encode(['where' => ['employee_id' => ['inq' => $arrId]]])];
                    $arrayPools[] = $pool->withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employees',$newFilter);
                }
                return $arrayPools;
            };
            $responses = \Illuminate\Support\Facades\Http::pool($fn);
            $results = [];
            foreach ($responses as $response) {
                if($response->successful()){
                    $item = $response->json();
                    foreach($item as $item) {
                        $results[$item['employee_id']] = $item;
                    }
                }
            }
            return $results;
        }
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

     public function getDepartments($params = [])
     {
        $whereArr = \Arr::only($params, ['department_id']);
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
      
        if(!empty($filter)) {
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/departments', ['filter' => json_encode([
                'where' => $filter,
            ])]);
        } else {
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/departments');
        }

         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     public function getDepartmentDetail($id)
     {
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/departments/'. $id);
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

     public function createNotification($notification = [], $arrEmployeeId = []) {
       
        $notiParams = \Arr::only($notification, ['name','type','title','content','created_time','description','is_all','brand_id','file','type_sms','sub_type','employee_id','send_time','is_processed','attachment']);
        if(!empty($arrEmployeeId)) {
            if(!is_array($arrEmployeeId)) {
                $arrEmployeeId = [$arrEmployeeId];
            }
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/notification-employees/to-employees',[
                'notification' => $notiParams,
                'employee_id'=> $arrEmployeeId 
            ]);
        } else {
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/notification-employees', $notiParams);
        }
       

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getDocuments($params = [])
    {
        $whereArr = \Arr::only($params, ['category_id', 'type_id', 'relate_type', 'relate_id', 'limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if ($k == 'relate_type' || $k == 'relate_id' || $k == 'limit' && $k == 'offset') continue;
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

        $params = ['filter' => json_encode($newFilter)];

        if(isset($whereArr['relate_type']) && $whereArr['relate_id']) {
            $params = [
                'relate_type'=> $whereArr['relate_type'],
                'relate_id' => $whereArr['relate_id'],
                'filter' => json_encode($newFilter)
            ];
        } 

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employee-documents', $params);
       
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;

    }
    
    public function getDocumentDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/employee-documents/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    
    public function getRecruitInterviews($params = [])
    {
        $whereArr = \Arr::only($params, ['interview_id','name','job_id','candidate_id','status','limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if ( $k == 'limit' && $k == 'offset') continue;
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

        $params = ['filter' => json_encode($newFilter)];

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/recruit-interviews', $params);
       
        if ($response->successful()) {
            return $response->json();
        }
        dd($response->body());
        \Log::error($response->body());
        return false;

    }
    
    public function getRecruitInterviewById($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/recruit-interviews/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getRecruitInterviewDetails($params = [])
    {
        $whereArr = \Arr::only($params, ['detail_id','interview_id','status','limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if ( $k == 'limit' && $k == 'offset') continue;
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

        $params = ['filter' => json_encode($newFilter)];

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/recruit-interview-details', $params);
       
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;

    }
    
    public function getRecruitInterviewDetailById($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/recruit-interview-details/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getRecruitCandidates($params = [])
    {
        $whereArr = \Arr::only($params, ['candidate_id','status','limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if ( $k == 'limit' && $k == 'offset') continue;
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

        $params = ['filter' => json_encode($newFilter)];

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/recruit-candidates', $params);
       
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;

    }
    
    public function getRecruitCandidateById($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/recruit-candidates/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getRecruitJobs($params = [])
    {
        $whereArr = \Arr::only($params, ['job_id','brand_id','department_id','status','limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        foreach($whereArr as $k => $v){
            if ( $k == 'limit' && $k == 'offset') continue;
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

        $params = ['filter' => json_encode($newFilter)];

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/recruit-jobs', $params);
       
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;

    }
    
    public function getRecruitJobById($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/recruit-jobs/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function createRecruitCandidate($candidate = []) {
       
        $candidateData = \Arr::only($candidate, [
            'candidate_id',
            'name',
            'phone',
            'email',
            'gender',
            'address',
            'birthdate',
            'skills',
            'city',
            'current_salary',
            'level',
            'introducer',
            'source',
            'detail',
            'created_by',
            'experience',
            'status',
            'updated_time',
            'rating',
            'category_id',
            'employee_id',
            'attachment',
            'avatar'
        ]);
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/recruit-candidates', $candidateData);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

}
