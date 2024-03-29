<?php
namespace Microservices\models\Notification;

use Illuminate\Support\Facades\Http;

class NotificationContact extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/notification/notification_contact';
        $this->setToken($options['token'] ?? 'system');
    }
}
