<?php
namespace Microservices;

use Illuminate\Support\Facades\Http;

class Notify
{
    protected $_url;
    public function __construct() {
        $this->url_sms = 'https://erp-api.ebomb.edu.vn/notification/send_sms';
        $this->url_mail = 'https://erp-api.ebomb.edu.vn/notification/send_mail';
    }

    //Send sms
    public function send_sms($params = array())
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $data = \Arr::only($params, ['channel','phone','description', 'type_sms']);
        $response = Http::post($this->url_sms, $data);
        return $response;
    }

    //send email
    public function send_email($params = array()) {
        $data = \Arr::only($params, ['channel','title','email', 'content']);
        $response = Http::post($this->url_mail, $data);
        return $response;
    }
}