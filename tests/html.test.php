<?php

Bundle::start('hybrid');

class HTMLTest extends PHPUnit_Framework_TestCase {

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

	/**
	 * Test Hybrid\HTML::raw()
	 *
	 * @test
	 */
	public function testRawExpression()
	{
		$output = Hybrid\HTML::raw('hello');
		$this->assertInstanceOf('Hybrid\Expression', $output);
	}

	/**
	 * Test Hybrid\HTML::pre_attributes()
	 *
	 * @test
	 */
	public function testPreAttributes()
	{
		$output   = Hybrid\HTML::pre_attributes(array('class' => 'span4 table'), array('id' => 'foobar'));
		$expected = array('id' => 'foobar', 'class' => 'span4 table');

		$this->assertEquals($expected, $output);

		$output   = Hybrid\HTML::pre_attributes(array('class' => 'span4 !span12'), array('class' => 'span12'));
		$expected = array('class' => 'span4');

		$this->assertEquals($expected, $output);

		$output   = Hybrid\HTML::pre_attributes(array('id' => 'table'), array('id' => 'foobar', 'class' => 'span4'));
		$expected = array('id' => 'table', 'class' => 'span4');

		$this->assertEquals($expected, $output);
	}
}