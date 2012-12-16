<?php

Bundle::start('hybrid');

class FormatTest extends PHPUnit_Framework_TestCase {

	/**
	 * Add data provider
	 * 
	 * @return array
	 */
	public static function providerArray()
	{
		return array(
			array(
				array(
					array('field1' => 'Value 1', 'field2' => 35, 'field3' => 123123),
					array('field1' => 'Value 1', 'field2' => "Value\nline 2", 'field3' => 'Value 3'),
				),
				'"field1","field2","field3"
"Value 1","35","123123"
"Value 1","Value
line 2","Value 3"',
			),
		);
	}

	/**
	 * Test for Format::make($foo, 'csv')->to_array()
	 *
	 * @test
	 * @dataProvider providerArray
	 */
	public function testFromCsv($array, $csv)
	{
		$this->assertEquals($array, Hybrid\Format::make($csv, 'csv')->to_array());
	}

	/**
	 * Test for Format::make($foo)->to_csv()
	 *
	 * @test
	 * @dataProvider providerArray
	 */
	public function testToCsv($array, $csv)
	{
		$this->assertEquals($csv, Hybrid\Format::make($array)->to_csv());
	}

	/**
	 * Test for Format::make($foo)->from_xml()
	 *
	 * @test
	 */
	public function testFromXml()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>

<phpunit colors="true" stopOnFailure="false" bootstrap="bootstrap_phpunit.php">
	<php>
		<server name="doc_root" value="../../"/>
		<server name="app_path" value="fuel/app"/>
		<server name="core_path" value="fuel/core"/>
		<server name="package_path" value="fuel/packages"/>
	</php>
	<testsuites>
		<testsuite name="core">
			<directory suffix=".php">../core/tests</directory>
		</testsuite>
		<testsuite name="packages">
			<directory suffix=".php">../packages/*/tests</directory>
		</testsuite>
		<testsuite name="app">
			<directory suffix=".php">../app/tests</directory>
		</testsuite>
	</testsuites>
</phpunit>';

		$expected = array (
			'@attributes' => array (
				'colors' => 'true',
				'stopOnFailure' => 'false',
				'bootstrap' => 'bootstrap_phpunit.php',
			),
			'php' => array (
				'server' => array (
					0 => array (
						'@attributes' => array (
							'name' => 'doc_root',
							'value' => '../../',
						),
					),
					1 => array (
						'@attributes' => array (
							'name' => 'app_path',
							'value' => 'fuel/app',
						),
					),
					2 => array (
						'@attributes' => array (
							'name' => 'core_path',
							'value' => 'fuel/core',
						),
					),
					3 => array (
						'@attributes' => array (
							'name' => 'package_path',
							'value' => 'fuel/packages',
						),
					),
				),
			),
			'testsuites' => array (
				'testsuite' => array (
					0 => array (
						'@attributes' => array (
							'name' => 'core',
						),
						'directory' => '../core/tests',
					),
					1 => array (
						'@attributes' =>
						array (
							'name' => 'packages',
						),
						'directory' => '../packages/*/tests',
					),
					2 => array (
						'@attributes' =>
						array (
							'name' => 'app',
						),
						'directory' => '../app/tests',
					),
				),
			),
		);

		$this->assertEquals(Hybrid\Format::make($expected)->to_php(), Hybrid\Format::make($xml, 'xml')->to_php());
	}

	/**
	 * Test for Format::make($foo)->to_array() when given array is empty
	 *
	 * @test
	 */
	function testToArrayGivenEmpty()
	{
		$array    = null;
		$expected = array();
		$this->assertEquals($expected, Hybrid\Format::make($array)->to_array());
	}
}