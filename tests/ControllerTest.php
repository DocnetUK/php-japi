<?php

require_once('Controllers/Example.php');
require_once('Controllers/Exceptional.php');

class ControllerTest extends PHPUnit_Framework_TestCase
{

    public function testBasicResponse()
    {
        $obj_controller = new Example();
        $obj_controller->dispatch();
        $this->assertEquals($obj_controller->getResponse(), ['test' => TRUE]);
    }

    public function testQuery()
    {
        $_GET['input1'] = 'value1';
        $obj_controller = new \Hello\World();
        $obj_controller->dispatch();
        $obj_response = $obj_controller->getResponse();
        $this->assertEquals($obj_response['input1'], 'value1');
    }

    public function testPost()
    {
        $_POST['input2'] = 'value2';
        $obj_controller = new \Hello\World();
        $obj_controller->dispatch();
        $obj_response = $obj_controller->getResponse();
        $this->assertEquals($obj_response['input2'], 'value2');
    }

    public function testParam()
    {
        $_GET['input3'] = 'value3';
        $_POST['input4'] = 'value4';
        $obj_controller = new \Hello\World();
        $obj_controller->dispatch();
        $obj_response = $obj_controller->getResponse();
        $this->assertEquals($obj_response['input3'], 'value3');
        $this->assertEquals($obj_response['input4'], 'value4');
    }

    public function testMixedParam()
    {
        $_GET['input4'] = 'value4-get';
        $_POST['input4'] = 'value4-post';
        $obj_controller = new \Hello\World();
        $obj_controller->dispatch();
        $obj_response = $obj_controller->getResponse();
        $this->assertEquals($obj_response['input4'], 'value4-get');
    }

}
