<?php

class JsonParams extends \Docnet\JAPI\Controller
{

    public function dispatch(){
        $this->setResponse([
            'json_param' => $this->getParam('json_param', 'default_value', true),
            'missing_param' => $this->getParam('missing_param', 'default_value', true)
        ]);
    }

    /**
     * Helper function to set the body
     *
     * @param string $str_body
     */
    public function setBody($str_body) {
        $this->str_request_body = $str_body;
    }
}