<?php
namespace Microservices;

class CrmV2
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm';
    }

    //Opportunity
    public function getOpportunitieDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/opportunities/'.$id);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    
    //Contact
    public function getContactDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/contacts/'.$id);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    
    public function getContacts($params= array())
    {
        $whereArr = \Arr::only($params, ['filter', 'page', 'limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/contacts', $params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    
    public function findContact($phoneOrEmail) {
        if (filter_var($phoneOrEmail, FILTER_VALIDATE_EMAIL)) {
            $contacts = $this->getContacts(['filter' => ['email' => $phoneOrEmail]]);
        }
        else {
            $phoneOrEmail = preg_replace('/^0/', '+84', $phoneOrEmail);
            $contacts = $this->getContacts(['filter' => ['phone' => $phoneOrEmail]]);
        }
        return $contacts ? $contacts : [];
    }

    //calendar
    public function getCalendars($params= array())
    {
        $whereArr = \Arr::only($params, ['filter', 'page', 'limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/calendars', $params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
}
