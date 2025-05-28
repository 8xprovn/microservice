<?php

namespace Microservices\Caches\Lms;

class Classes extends \Microservices\Caches\BaseCache
{
    protected $service = 'lms';
    protected $table = 'edu_class';
    public function __construct($options = []) {
    }
}
