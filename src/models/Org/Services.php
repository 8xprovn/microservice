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
            ],
            [
                'code' => 'erp_task_backend',
                'name' => 'Backend PM',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_system_backend',
                'name' => 'Backend hệ thống',
                'url' => '/systems',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_payroll_backend',
                'name' => 'Backend PM',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'ebomb_id',
                'name' => 'Authen Ebomb',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_notification',
                'name' => 'Notification',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_app_backend',
                'name' => 'Backend app',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_inventory_backend',
                'name' => 'Quản lý kho',
                'url' => '/inventory',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'ielts_fighter_backend',
                'name' => 'Ielts Fighter Backend',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'inner_training',
                'name' => 'Đào tạo nội bộ',
                'url' => '/inner-training',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'dinotech_backend',
                'name' => 'Dinotech Backend',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_callcenter_v2',
                'name' => 'Call center',
                'url' => '/call-center',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'lalakid_cms',
                'name' => 'LALAKID CMS',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'authenticate_lalakids',
                'name' => 'Ebomb ID New',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_knowledge',
                'name' => 'Knowledge',
                'url' => '/knowledge',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'test_platform',
                'name' => 'Hệ thống Test',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_crm_backend_v2',
                'name' => 'Backend CRM v2',
                'url' => '/crm',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_authorization_backend_v2',
                'name' => 'Phân quyền quản trị V2',
                'url' => '/authorization',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'test_platform_v2',
                'name' => 'Test platform V2',
                'url' => '/',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_org_backend',
                'name' => 'Backend ORG',
                'url' => '/org',
                'icon' => 'icon-users4'
            ],
            [
                'code' => 'erp_test_backend',
                'name' => '	Test backend',
                'url' => '/tests',
                'icon' => 'icon-users4'
            ]
        ];
    }
}
