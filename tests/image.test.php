<?php

class TestImage extends PHPUnit_Framework_TestCase 
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