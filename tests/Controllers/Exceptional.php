<?php

class Exceptional extends \Docnet\JAPI\Controller
{
    public function dispatch(){
        throw new RuntimeException('Error Message', 400);
    }
}