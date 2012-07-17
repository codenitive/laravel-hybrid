<?php

class HtmlMemory extends PHPUnit_Framework_TestCase 
{
	/**
	 * Setup: Start Hybrid Bundle
	 *
	 * @return  void
	 */
	public function setup()
	{
		Bundle::start('hybrid');
	}

	/**
	 * Test Hybrid\HTML::create() with content
	 * 
	 * @test
	 */
	public function testCreateWithContent()
	{
		$expected = '<div class="foo">Bar</div>';
		$output   = Hybrid\HTML::create('div', 'Bar', array('class' => 'foo'));

		$this->assertEquals($expected, $output);
	}

	/**
	 * Test Hybrid\HTML::create() without content
	 * 
	 * @test
	 */
	public function testCreateWithoutContent()
	{
		$expected = '<img src="hello.jpg" class="foo">';
		$output   = Hybrid\HTML::create('img', array('src' => 'hello.jpg', 'class' => 'foo'));

		$this->assertEquals($expected, $output);

		$expected = '<img src="hello.jpg" class="foo">';
		$output   = Hybrid\HTML::create('img', null, array('src' => 'hello.jpg', 'class' => 'foo'));

		$this->assertEquals($expected, $output);
	}
}