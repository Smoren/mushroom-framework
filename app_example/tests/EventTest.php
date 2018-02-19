<?php

class EventTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        Event::register('test', function(&$data=null) {
        	$data = 'ok';
        });
    }

    /** 
    * @dataProvider providerTrigger
    */
    public function testTrigger($data) {
    	Event::trigger('test', $data);
    	if($data !== 'ok') $this->fail('изменения не внесены');
    }

    public function providerTrigger() {
    	return array(
    		array('fail'),
    		array(array()),
    		array(false),
    	);
    }
}