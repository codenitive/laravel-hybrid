<?php

class ImageTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setup()
	{
		Bundle::start('hybrid');
	}

	/**
	 * Test that Hybrid\Image::make() return an instanceof Hybrid\Image.
	 * 
	 * @test
	 * @return  void
	 */
	public function testMake()
	{
		$this->assertInstanceOf('Hybrid\Image\Gd', Hybrid\Image::make(array(
			'driver' => 'Gd',
		)));

		$this->assertInstanceOf('Hybrid\Image\Imagick', Hybrid\Image::make(array(
			'driver' => 'Imagick',
		)));

		$this->assertInstanceOf('Hybrid\Image\Imagemagick', Hybrid\Image::make(array(
			'driver' => 'Imagemagick',
		)));
	}
}