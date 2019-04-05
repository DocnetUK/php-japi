<?php

require_once('Controllers/Example.php');
require_once('Controllers/Headers.php');
require_once('Controllers/Exceptional.php');
require_once('Controllers/JsonParams.php');
require_once('Controllers/ProtectedFunctions.php');

class ControllerTest extends PHPUnit_Framework_TestCase
{

    public function testBasicResponse()
    {
        $obj_controller = new Example();
        $obj_controller->dispatch();
        $this->assertEquals($obj_controller->getResponse(), ['test' => true]);
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

    public function testCliHeaders()
    {
        $_SERVER['HTTP_SOME_HEADER'] = true;
        $obj_controller = new Headers();
        $obj_controller->dispatch();
        $this->assertEquals($obj_controller->getResponse(), ['Some-Header' => true]);
    }

    public function testJsonBodyParam()
    {
        $str_json = '{"json_param": "param_found"}';
        $obj_controller = new \JsonParams();
        $obj_controller->setBody($str_json);
        $obj_controller->dispatch();
        $obj_response = $obj_controller->getResponse();
        $this->assertEquals('param_found', $obj_response['json_param']);
        $this->assertEquals('default_value', $obj_response['missing_param']);
    }

    public function testIsPost() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $obj_controller = new ProtectedFunctions();
        $this->assertTrue($obj_controller->getIsPost());
    }
}
