<?php

class ProtectedFunctions extends \Docnet\JAPI\Controller
{

    public function dispatch(){
        $this->setResponse(true);
    }


    public function getIsPost() {
        return $this->isPost();
    }
}