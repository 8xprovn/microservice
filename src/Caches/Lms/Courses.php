<?php

namespace Microservices\Caches\Lms;

class Courses extends \Microservices\Caches\BaseCache
{
    protected $service = 'lms';
    protected $table = 'edu_course';
    public function __construct($options = []) {
    }
}
