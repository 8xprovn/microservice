<?php
namespace Microservices;


class Finance
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/finance';
    }

     public function getInvoices($params = array())
     {
         $whereArr = \Arr::only($params, ['invoice_id', 'employee_id' , 'contact_id', 'payment_contact_id']);
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
 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/invoices',['filter' => json_encode([
             'where' => $filter,
             //'fields' => ['title ','total_amt','discount_amt','original_amt', 'debt_amt', 'discount_code', 'discount_coupon_amt', 'discount_other_amt' ]
             ])]);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
    }
    public function getInvoiceDetailsByInvoice($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/invoices/'.$id.'/invoice-details/');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getPaymentsByInvoice($id) {
        //var_dump(['filter' => json_encode(['where' => $filter])]); die;
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/invoices/'.$id.'/invoice-payments/');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getInvoiceDetail($id,$options = [])
     {
        $params = [];
        if (!empty($options['include'])) {
            $params['include'] = $options['include'];
        }
        if ($params) {
            $params = ['filter' => json_encode($params)];
        }
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/invoices/'.$id,$params);

        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
     }

     public function getInvoiceDetailById($id)
     {
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/invoice-details/'.$id);

        if ($response->successful()) {
             return $response->json();
        }

        \Log::error($response->body());
        return false;
     }

}