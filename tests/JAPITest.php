<?php

require_once('Controllers/Example.php');
require_once('Controllers/Exceptional.php');
require_once('Controllers/Whoops.php');
require_once('Controllers/AccessDenied.php');

class JAPITest extends PHPUnit_Framework_TestCase
{

    /**
     * Test the dispatch() cycle
     */
    public function testDispatchCycle()
    {
        // Mocked controller & expectations
        $obj_controller = $this->getMockBuilder('\\Example')->getMock();
        $obj_controller->expects($this->once())->method('preDispatch');
        $obj_controller->expects($this->once())->method('dispatch');
        $obj_controller->expects($this->once())->method('postDispatch');

        // Mock JAPI (just replace the sendResponse method to avoid output errors)
        $obj_japi = $this->getMockBuilder('\\Docnet\\JAPI')->setMethods(['sendResponse'])->getMock();

        // Dispatch
        $obj_japi->dispatch($obj_controller);
    }

    /**
     * Validate the bootstrap() cycle works on a supplied Controller
     */
    public function testConcreteBootstrapCycle()
    {
        // Mocked controller & expectations
        $obj_controller = $this->getMockBuilder('\\Example')->getMock();
        $obj_controller->expects($this->once())->method('preDispatch');
        $obj_controller->expects($this->once())->method('dispatch');
        $obj_controller->expects($this->once())->method('postDispatch');

        // Mock JAPI (just replace the sendResponse method to avoid output errors)
        $obj_japi = $this->getMockBuilder('\\Docnet\\JAPI')->setMethods(['sendResponse'])->getMock();
        $obj_japi->expects($this->once())->method('sendResponse');

        // Dispatch
        $obj_japi->bootstrap($obj_controller);
    }

    /**
     * Test the bootstrap() methods correctly executes the supplied callback
     *
     * @todo Implement this test!
     */
    public function testBootstrapCallback()
    {
    }

    /**
     * Test Exceptions are correctly passed to jsonError from the bootstrap() method
     */
    public function testBootstrapErrorCycle()
    {
        // Mock JAPI
        $obj_japi = $this->getMockBuilder('\\Docnet\\JAPI')->setMethods(['sendResponse', 'jsonError'])->getMock();
        $obj_japi->expects($this->never())->method('sendResponse');
        $obj_japi->expects($this->once())->method('jsonError')->with(
            $this->equalTo(new Exception()),
            $this->equalTo(0)
        );

        // Dispatch
        $obj_japi->bootstrap(new Whoops());
    }

    /**
     * Test custom Exception codes are correctly passed to jsonError from the bootstrap() method
     */
    public function testBootstrapCustomErrorCycle()
    {
        // Mock JAPI
        $obj_japi = $this->getMockBuilder('\\Docnet\\JAPI')->setMethods(['sendResponse', 'jsonError'])->getMock();
        $obj_japi->expects($this->never())->method('sendResponse');
        $obj_japi->expects($this->once())->method('jsonError')->with(
            $this->equalTo(new RuntimeException('Error Message', 400)),
            $this->equalTo(400)
        );

        // Dispatch
        $obj_japi->bootstrap(new Exceptional());
    }

    /**
     * Validate the response data from the Controller is correctly passed to sendResponse()
     */
    public function testSendResponse()
    {
        // Mock JAPI
        $obj_japi = $this->getMockBuilder('\\Docnet\\JAPI')->setMethods(['sendResponse'])->getMock();
        $obj_japi->expects($this->once())->method('sendResponse')->with(
            $this->equalTo(['test' => true]),
            $this->equalTo(200)
        );

        // Dispatch
        $obj_japi->bootstrap(new Example());
    }

    /**
     * Do we correctly call the logger in jsonError scenarios?
     */
    public function testLogger()
    {
        // Mock the logger
        $obj_logger = $this->getMockBuilder('\\Psr\\Log\\NullLogger')->setMethods(['log'])->getMock();
        $obj_logger->expects($this->once())->method('log')->with(
            $this->equalTo(\Psr\Log\LogLevel::ERROR)
        );

        // Mock JAPI
        $obj_japi = $this->getMockBuilder('\\Docnet\\JAPI')->setMethods(['sendResponse'])->getMock();
        $obj_japi->setLogger($obj_logger);

        // Dispatch
        $obj_japi->bootstrap(new Exceptional());
    }

    /**
     * Ensure we do not fall over when no logger has been supplied
     */
    public function testNoLogger()
    {
        // Mock JAPI
        $obj_japi = $this->getMockBuilder('\\Docnet\\JAPI')->setMethods(['sendResponse'])->getMock();

        // Dispatch
        $obj_japi->bootstrap(new Exceptional());
    }

    /**
     * Test an AccessDenied Exception codes are correctly passed to jsonError from the bootstrap() method
     */
    public function testBootstrapAccessDeniedErrorCycle()
    {
        // Mock JAPI
        $obj_japi = $this->getMockBuilder('\\Docnet\\JAPI')->setMethods(['sendResponse', 'jsonError'])->getMock();
        $obj_japi->expects($this->never())->method('sendResponse');
        $obj_japi->expects($this->once())->method('jsonError')->with(
            $this->equalTo(new \Docnet\JAPI\Exceptions\AccessDenied('Error Message', 403)),
            $this->equalTo(403)
        );

        // Dispatch
        $obj_japi->bootstrap(new AccessDenied());
    }

}
