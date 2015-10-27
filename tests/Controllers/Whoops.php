<?php

class Whoops extends \Docnet\JAPI\Controller
{
    public function dispatch(){
        throw new Exception;
    }
}