<?php

namespace Microservices\models\Finance;

class InvoicesCancel extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance/invoice_detail_cancel';
        
    }
    protected $service = 'erp_finance_backend_v2';
    protected $table = 'invoice_detail_cancel';
}
