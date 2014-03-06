<?php

namespace app\model;

class User
{
    public $name = null;
    
    public function __construct($name) {
        $this->name = $name;
    }
}