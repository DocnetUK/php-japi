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

    private function mockJapiForJsonError($arr_resp, $int_code)
    {
        // Mock JAPI
        $obj_japi = $this->getMockBuilder('\\Docnet\\JAPI')->setMethods(['sendResponse'])->getMock();
        $obj_japi->expects($this->once())->method('sendResponse')->with(
            $this->equalTo($arr_resp),
            $this->equalTo($int_code)
        );
        return $obj_japi;
    }

    /**
     * Test E_USER_ERROR
     */
    public function testUserError()
    {
        $obj_japi = $this->mockJapiForJsonError(['code' => E_USER_ERROR, 'msg' => 'Internal Error'], 500);
        $obj_japi->bootstrap(function(){
            trigger_error('Test E_USER_ERROR', E_USER_ERROR);
        });
    }

    /**
     * Test invalid code
     */
    public function testNonObjectError()
    {
        $obj_japi = $this->mockJapiForJsonError(['code' => E_RECOVERABLE_ERROR, 'msg' => 'Internal Error'], 500);
        $obj_japi->bootstrap(function(){
            // Nonsense code (on purpose)
            $this->$this->wer();
        });
    }


}