<?php

require_once 'Email.php';
class EmailTest extends PHPUnit_Framework_TestCase
{
    public $test;
    public $results;
    public function setup()
    {
        $this->test = new email("fakeuser@f9349i3.xyz");
    }
    public function testEmail()
    {
        $unit = $this->test->getResults();
        $this->assertFalse(false, $unit);
    }
}

?>