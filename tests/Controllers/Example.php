<?php

class Example extends \Docnet\JAPI\Controller
{
    public function dispatch(){
        $this->setResponse(['test' => true]);
    }
}