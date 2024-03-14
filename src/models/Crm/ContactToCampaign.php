<?php

namespace Microservices\models\Crm;

use Illuminate\Support\Arr;

class ContactToCampaign extends \Microservices\models\Model
{
    protected $_url;
    //protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm/contacts-campaign';
        $this->setToken($options['token'] ?? 'system');
    }
    public function getContacts($params) {
        $contact_ids = \Microservices::Crm('ContactToCampaign')->all(['campaign_id' => $params], ['limit' => 500]);
        if(!empty($contact_ids)) {
            $contact_ids = collect($contact_ids)->pluck('_id')->unique()->values()->all();
            return array_unique($contact_ids);
        }
        return [];
    }
}
