<?php
namespace Microservices;


class Lms
{
    protected $_url;
    protected $_hash_secret;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/edu';
        $this->_hash_secret = env('TEST_HASH_SECRET');
    }

    //COURSE PRICE
    public function getCoursePrices($params = array())
    {
        $whereArr = \Arr::only($params, ['course_id','price_id','type']);
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
        $filter = array_merge($filter, ['status' => 'active','from_date' => ['lte' => date("Y-m-d")],'to_date' => ['gte' => date('Y-m-d')]]);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/course-prices',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['name','course_id','amount','price_id','validation']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    public function getCourseIncludePrice($params = array()) {
        $whereArr = \Arr::only($params, ['course_id']);
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
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/courses',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'eduCoursePrices',
                    'scope' => [
                        'fields'=> ['course_id','amount','price_id','validation'],
                        'where' => ['status' => 'active','from_date' => ['lte' => date("Y-m-d")],'to_date' => ['gte' => date('Y-m-d')]]
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
    

    //CLASS
    public function getClass($params = array())
    {
        $whereArr = \Arr::only($params, ['class_id']);
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
        $filter = array_merge($filter, ['status' => 'opened']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/classes',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['class_id','code','name']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getClassIncludeStudent($params = array()) {
        $whereArr = \Arr::only($params, ['class_id']);
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
        $filter = array_merge($filter, ['status' => 'opened']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/classes',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'eduStudents',
                    'scope' => [
                        //'fields'=> ['student_id','contact_id','course_id','invoice_detail_id', 'invoice_id'],
                        'where' => ['status' => 'active']
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



    public function getClassDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/classes/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    //COURSE 
    public function getCourse($params = array())
    {
        $whereArr = \Arr::only($params, ['course_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/courses',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['course_id','name','description']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getCourseIncludeLesson($params = array()) {
        $whereArr = \Arr::only($params, ['course_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/courses',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'eduCourseLessons'
                ]
            ],
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getCourseIncludeLevel($params = array()) {
        $whereArr = \Arr::only($params, ['course_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/courses',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'courseLevel',
                    'scope' => [
                        'fields'=> ['course_level_id','name','course_id','brand_id']
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



    public function getCourseDetail($id)
    {

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/courses/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    //STUDENT
    public function getStudent($params = array())
    {
        $whereArr = \Arr::only($params, ['student_id', 'contact_id', 'class_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/students',['filter' => json_encode([
            'where' => $filter,
            //'fields' => ['class_id','code','name']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getStudentIncludeClass($params = array()) {
        $whereArr = \Arr::only($params, ['student_id', 'contact_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/students',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'classes',
                    'scope' => [
                        'fields'=> ['class_id','code','name','brand_id', 'status' ,'price', 'number_of_students']
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

    public function getStudentDetail($id)
    {

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/students/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    //NEWS
     public function getNews($params = array())
     {
         $whereArr = \Arr::only($params, ['news_id']);
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
         $filter = array_merge($filter, ['publish' => 1]);
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/news',['filter' => json_encode([
             'where' => $filter,
             //'fields' => ['news_id ','title','description']
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function getNewsIncludeCategories($params = array()) {
         $whereArr = \Arr::only($params, ['news_id']);
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
         $filter = array_merge($filter, ['publish' => 1]);
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/news',['filter' => json_encode([
             'where' => $filter,
             'include' => [
                 [
                     'relation' => 'newsCategories',
                     'scope' => [
                         'fields'=> ['category_id','name','description', 'parent' ,'brand_id', 'images', 'slug']
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

     public function getNewDetail($id)
     {
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/news/'.$id);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     //CATEGORY

     public function getCategories($params = array())
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
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/news-categories',['filter' => json_encode([
             'where' => $filter,
             //'fields' => ['news_id ','title','description']
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function getCategoriesIncludeNews($params = array()) {
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
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/news-categories',['filter' => json_encode([
             'where' => $filter,
             'include' => [
                 [
                     'relation' => 'news',
                     'scope' => [
                         'fields'=> ['news_id','title','description', 'detail' ,'image', 'publish', 'publish_time']
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

     public function getCategoryDetail($id)
     {
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/news-categories/'.$id);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }


     //QUESTION

     public function getQuestions($params = array())
     {
         $whereArr = \Arr::only($params, ['question_id']);
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
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/questions',['filter' => json_encode([
             'where' => $filter,
             //'fields' => ['news_id ','title','description']
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function getQuestionsIncludeAnswers($params = array()) {
         $whereArr = \Arr::only($params, ['question_id']);
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
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/questions',['filter' => json_encode([
             'where' => $filter,
             'include' => [
                 [
                     'relation' => 'questionAnswers',
                    //  'scope' => [
                    //      'fields'=> ['answer_id ','content','question_id ', 'params' ,'number_question', 'answers', 'options']
                    //  ]
                 ]
             ],
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     public function getQuestionDetail($id)
     {
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/questions/'.$id);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     //SURVEYS
     public function getSurveys($params = array())
     {
         $whereArr = \Arr::only($params, ['survey_id']);
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
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/surveys',['filter' => json_encode([
             'where' => $filter,
             //'fields' => ['news_id ','title','description']
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function getSurveysIncludeResults($params = array()) {
         $whereArr = \Arr::only($params, ['survey_id']);
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
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/surveys',['filter' => json_encode([
             'where' => $filter,
             'include' => [
                 [
                     'relation' => 'surveyResults',
                    //  'scope' => [
                    //      'fields'=> ['id', 'survey_id ','label','type ', 'value']
                    //  ]
                 ]
             ],
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     public function getSurveyDetail($id)
     {
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/surveys/'.$id);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     //TEST
     public function getTests($params = array())
     {
         $whereArr = \Arr::only($params, ['test_id', 'parent_id']);
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
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tests',['filter' => json_encode([
             'where' => $filter,
             //'fields' => ['test_id  ','title','description']
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }
 
     public function getTestsIncludeLogs($params = array()) {
         $whereArr = \Arr::only($params, ['test_id']);
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
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tests',['filter' => json_encode([
             'where' => $filter,
             'include' => [
                 [
                     'relation' => 'testLogs',
                    //  'scope' => [
                    //      'fields'=> ['logs_id ', 'contact_id  ','question_list','score ', 'answer_list']
                    //  ]
                 ]
             ],
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     public function getTestsIncludeQuestions($params = array()) {
        $whereArr = \Arr::only($params, ['test_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tests',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'questions',
                   //  'scope' => [
                   //      'fields'=> ['question_id', 'title','images','detail', 'user_id','sound', 'publish']
                   //  ]
                ]
            ],
            ])]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

     public function getTestDetail($id)
     {
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tests/'.$id);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     public function getCostLevelWithType($type)
     {
        $where['type'] = $type;
        $filter = ['filter' => json_encode([
            'where' => $where
        ])];

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/cost-levels/', $filter);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
     }

     public function getClassSchedule($params = [])
     {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/class-schedules/', http_build_query($params));
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
     }

     public function getQuestionByTest($test_id=0) {
        if ((int)$test_id > 0) {
            $hash =  hash('sha256', $test_id . $this->_hash_secret);
            $response = \Http::withToken(env('API_GATEWAY_TOKEN',''))->get(env('API_GATEWAY_URL').'/edu/tests/'.$test_id.'/questions?hash='.$hash);
            if ($response->successful()) {
                return $response->json();
            }

            \Log::error($response->body());
            return false;
        }
        return false;
    }

    public function createTestLog($test_id, $contact_id, $question_list = [], $test_parent_id = null , $logs_parent_id = null, $is_group = 0, $relate_type = null, $relate_id = null) {
        if(empty($test_id) || empty($contact_id) || empty($question_list)) {
            return false;
        }
        $response = \Http::withToken(env('API_GATEWAY_TOKEN',''))->post(env('API_GATEWAY_URL').'/edu/test-logs',[
            'test_id' => $test_id,
            'contact_id' => $contact_id,
            'question_list'=> $question_list,
            'test_parent_id' => $test_parent_id,
            'logs_parent_id' => $logs_parent_id,
            'is_group' => $is_group,
            'relate_type' => $relate_type,
            'relate_id' => $relate_id
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        \Log::error($response->body());
        return false;
    }

    public function updateTestLog($test_id, $logs_id, $logs_token, $answers = []) {
        
        $response = \Http::withToken(env('API_GATEWAY_TOKEN',''))->post(env('API_GATEWAY_URL').'/edu/test',[
            'test_id' => $test_id,
            'logs_id' => $logs_id,
            'logs_token' => $logs_token,
            'answers' => $answers,
        ]);

        if ($response->successful()) {
            return $response->json();
        }
        
        \Log::error($response->body());
        return false;
    }


    public function getTestLogDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/test-logs/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getRoadMap($params = [])
    {
        $whereArr = \Arr::only($params, ['brand_id', 'limit', 'offset']);
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
       
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/roadmaps',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getRoadMapDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/roadmaps/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getRoadMapCourse($params = [])
    {
        $whereArr = \Arr::only($params, ['course_id', 'roadmap_id', 'limit', 'offset']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/roadmap-courses',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getRoadMapCourseDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/roadmap-courses/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function createTestShort($test_id, $question_list = [], $answers = []) {
        if(empty($test_id) || empty($question_list) || empty($answers)) {
            return false;
        }
        $response = \Http::withToken(env('API_GATEWAY_TOKEN',''))->post(env('API_GATEWAY_URL').'/edu/tests/short',[
            'test_id' => $test_id,
            'question_list'=> $question_list,
            'answers' => $answers,
        ]);
   
        if ($response->successful()) {
            return $response->json();
        }

        \Log::error($response->body());
        return false;
    }

    public function getClassByCourse($params = [])
    {
        $whereArr = \Arr::only($params, ['class_id', 'status', 'course_id', 'start_date', 'limit', 'offset']);

        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        $filter = [];
        foreach($whereArr as $k => $v){
            if($k == 'limit' || $k == 'offset') continue;
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    } else if($k == 'start_date') {
                        $filter[$k] = ['gte' => $v];
                    }
                    else {
                        $filter[$k] = ['eq' => $v];
                    }
                    break;
            }
        }

        if(!isset($whereArr['status'])) {
            $filter = array_merge($filter, ['status' => 'opened']);
        }

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/classes',['filter' => json_encode([
            'where' => $filter,
            'limit' => $limit,
            'offset' => $offset,
            //'fields' => ['class_id','code','name']
            ])]);
        if ($response->successful()) {
            return $response->json();
        }

      
        \Log::error($response->body());
        return false;
    }

    public function getSettingShifts($params = [])
    {
        $whereArr = \Arr::only($params, ['shift_id', 'limit', 'offset']);

        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;

        $filter = [];
        foreach($whereArr as $k => $v){
            if($k == 'limit' || $k == 'offset') continue;
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    } else {
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

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/setting-shifts',['filter' => json_encode($newFilter)]);
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
}