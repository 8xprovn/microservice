<?php

namespace Microservices\models\Org;

use Illuminate\Support\Arr;

class Services extends \Microservices\models\Model
{
    public function __construct($options = []) {
        
    }
    public function all($params = [], $options = []) {
        return [
            [
                'code' => 'erp_finance_backend_v2',
                'name' => 'Tài chính',
                'url' => '/finance',
                'icon' => 'icon-cash'
            ],
            [
                'code' => 'erp_lms_backend_v2',
                'name' => 'Đào tạo',
                'url' => '/lms',
                'icon' => 'icon-design'
            ],
            [
                'code' => 'erp_hr_backend_v2',
                'name' => 'Nhân sự',
                'url' => '/hr',
                'icon' => 'icon-users4'
            ]
        ];
    }
}
