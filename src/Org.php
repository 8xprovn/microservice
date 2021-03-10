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
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brand-branches/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    public function getBranchs($params)
    {
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

    public function getBranchsIncludeBrands($params) {
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

    public function getBrands($params)
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

    public function getBrandsByBranch($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/brand-branches/'.$id.'/brands/');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }


    public function getBrandsIncludeBranchs($params) {
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

    public function getLocations($params)
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

    public function getLocationsIncludeDistricts($params) {
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

     public function getDistricts($params)
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

     public function getDistrictsIncludeCommunes($params) {
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

     public function getCommunes($params)
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
 




    
}