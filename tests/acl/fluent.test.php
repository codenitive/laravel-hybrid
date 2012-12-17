<?php

Bundle::start('hybrid');

class AclFluentTest extends PHPUnit_Framework_TestCase {
	
	public function testIncompleted()
	{
		$this->markTestIncomplete('incompleted');
	}
}