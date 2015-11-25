<?php
/**
 * Test JAPI error handling
 */
class ErrorTest extends PHPUnit_Framework_TestCase
{

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

}