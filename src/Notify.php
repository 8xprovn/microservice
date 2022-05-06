<?php
namespace Microservices;

use Illuminate\Support\Facades\Http;

class Notify
{
    protected $_url;
    public function __construct() {
        $this->_url = 'https://erp-api.ebomb.edu.vn/notification';
        $this->url_sms = 'https://erp-api.ebomb.edu.vn/notification/send_sms';
        $this->url_mail = 'https://erp-api.ebomb.edu.vn/notification/send_mail';
        $this->url_mobile = 'https://erp-api.ebomb.edu.vn/notification/send_mobile';
    }
    public function send($params) {
        switch($params['type']) {
            default:
            $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->post($this->_url.'/send_'.$params['type'],$params);
            if ($response->successful()) {
                return true;
            }
            return ['message' => $response->body()];
        }
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
        $data = \Arr::only($params, ['channel','title','email', 'content', 'attachment', 'cc_email']);
        $response = Http::post($this->url_mail, $data);
        return $response;
    }

    //send mobile
    public function send_mobile($params = array()) {
        $data = \Arr::only($params, ['channel','title','email', 'description', 'content', 'badge', 'token', 'url', 'notification_id', 'sound']);
        $response = Http::post($this->url_mobile, $data);
        return $response;
    }
}
