<?php
namespace Microservices;

class Org
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/org';
    }
    public function getBranchDetail($id)
    {
        if (is_array($id)) {
            return \Cache::rememberMany($id,'org:branch:detail:',env('CACHE_EXPIRE_DEFAULT'),function($arrKeyNotCache){
                $arrBranch = $this->getBranchs(['branch_id' => $arrKeyNotCache]);
                if (!$arrBranch) {
                    return false;
                }
                return array_combine(array_column($arrBranch, 'branch_id'), $arrBranch);
            });
        }
        return \Cache::remember('org:branch:detail:'.$id,env('CACHE_EXPIRE_DEFAULT'),function() use ($id){
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brand-branches/'.$id);
            if ($response->successful()) {
                return $response->json();
            }
            return [];
        });
    }
    public function getBranchs($params = array())
    {
        $whereArr = \Arr::only($params, ['branch_id', 'city_code', 'manager_id']); 
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
        $q = '';
        $q = ($filter) ? ['filter' => json_encode([
                'where' => $filter
            ])] : '';  
              
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brand-branches', $q);
        if ($response->successful()) {
            return $response->json();
        }

        
        \Log::error($response->body());
        return false;
    }

    
    public function getBranchsByBrand($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brands/'.$id.'/brand-branches/');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getBranchsIncludeBrands($params = array()) {
        $whereArr = \Arr::only($params, ['branch_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brand-branches',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'brands',
                   //  'scope' => [
                   //      'fields'=> ['brand_id', 'name','description','status', 'images']
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


    //

    public function getBrands($params = array())
    {
        $whereArr = \Arr::only($params, ['brand_id']);
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
        $q = '';
        $q = ($filter) ? ['filter' => json_encode([
                'where' => $filter
            ])] : '';        
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brands', $q);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    public function getBrandDetail($id)
    {

        if (is_array($id)) {
            return \Cache::rememberMany($id,'org:brand:detail:',env('CACHE_EXPIRE_DEFAULT'),function($arrKeyNotCache){
                $arrBrand = $this->getBrands(['brand_id' => $arrKeyNotCache]);
                if (!$arrBrand) {
                    return false;
                }
                return array_combine(array_column($arrBrand, 'brand_id'), $arrBrand);
            });
        }
        return \Cache::remember('org:brand:detail:'.$id,env('CACHE_EXPIRE_DEFAULT'),function() use ($id){
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brands/'.$id);
            if ($response->successful()) {
                return $response->json();
            }
            return [];
        });
    }

    public function getBrandsByBranch($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brand-branches/'.$id.'/brands/');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getBrandsIncludeBranchs($params = array()) {
        $whereArr = \Arr::only($params, ['brand_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brands',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'brandBranches',
                   //  'scope' => [
                   //      'fields'=> ['branch_id', 'name','address','city_code', 'status','brand_id ']
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

    //Location city

    public function getLocations($params = array())
    {
        $whereArr = \Arr::only($params, ['city_id']);
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
        $q = '';
        $q = ($filter) ? ['filter' => json_encode([
                'where' => $filter
            ])] : '';        
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/location-cities', $q);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getLocationsIncludeDistricts($params = array()) {
        $whereArr = \Arr::only($params, ['city_id']);
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
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/location-cities',['filter' => json_encode([
            'where' => $filter,
            'include' => [
                [
                    'relation' => 'locationDistricts',
                   //  'scope' => [
                   //      'fields'=> ['district_id', 'name','type','city_id']
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

    public function getLocationDetail($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/location-cities/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

     //Location district

     public function getDistricts($params = array())
     {
         $whereArr = \Arr::only($params, ['district_id']);
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
         $q = '';
         $q = ($filter) ? ['filter' => json_encode([
                 'where' => $filter
             ])] : '';        
         //var_dump(['filter' => json_encode(['where' => $filter])]); die;
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/location-cities', $q);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     public function getDistrictsIncludeCommunes($params = array()) {
         $whereArr = \Arr::only($params, ['district_id']);
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
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/location-districts',['filter' => json_encode([
             'where' => $filter,
             'include' => [
                 [
                     'relation' => 'locationCommunes',
                    //  'scope' => [
                    //      'fields'=> ['district_id', 'name','type','city_id']
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

     public function getDistrictDetail($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/location-districts/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

     public function getCommunes($params = array())
     {
         $whereArr = \Arr::only($params, ['commune_id']);
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
         $q = '';
         $q = ($filter) ? ['filter' => json_encode([
                 'where' => $filter
             ])] : '';        
         //var_dump(['filter' => json_encode(['where' => $filter])]); die;
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/location-communes', $q);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     public function getCommuneDetail($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/location-communes/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
 

    public function getSystemServices($params = array())
     {
         $whereArr = \Arr::only($params, ['service_id']);
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
         $q = '';
         $q = ($filter) ? ['filter' => json_encode([
                 'where' => $filter
             ])] : '';        
         //var_dump(['filter' => json_encode(['where' => $filter])]); die;
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/system-services', $q);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
     }

     public function getSystemServiceDetail($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/system-services/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getSystemLogs($params = [])
    {
        $whereArr = \Arr::only($params, ['log_id','relate_type','relate_id', 'limit', 'offset']);
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
       
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/system-logs',['filter' => json_encode($newFilter)]);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;

    }

    public function getSystemLogDetail($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/system-logs/'.$id);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getCities($params = [])
    {
        $whereArr = \Arr::only($params, ['city_id', 'status', 'name','city_code', 'type','limit', 'offset']);
        $filter = [];
        $limit = isset($whereArr['limit']) && $whereArr['limit'] > 0 ? $whereArr['limit'] : 200;
        $offset = isset($whereArr['offset']) && $whereArr['offset'] > 0 ? $whereArr['offset'] : 0;
        $status = isset($whereArr['status']) ? $whereArr['status'] : 'active';
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
        $filter = array_merge($filter, ['status' => $status]);
        
     
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/cities',['filter' => json_encode([
            'limit' => $limit,
            'offset' => $offset,
            'where' => $filter,
        ])]);
        if ($response->successful()) {
            return $response->json();
        }

        \Log::error($response->body());
        return false;

    }
    public function getLocDetailCity($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/cities/'.$id);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

   
   
}