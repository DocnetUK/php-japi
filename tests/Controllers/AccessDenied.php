<?php

class AccessDenied extends \Docnet\JAPI\Controller
{
    public function dispatch(){
        throw new \Docnet\JAPI\Exceptions\AccessDenied('Error Message', 403);
    }
}