<?php

namespace Moneybird;

require_once dirname(__FILE__) . '/../../ApiConnector.php';

/**
 * Test class for Product_Service.
 * Generated by PHPUnit on 2012-04-22 at 14:43:05.
 */
class Product_ServiceTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Product_Service
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		include ('../config.php');
		
		$transport = getTransport($config);	
		$mapper = new XmlMapper();
		$connector = new ApiConnector($config['clientname'], $transport, $mapper);
		$this->object = $connector->getService('Product');
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		
	}

	/**
	 * @covers Moneybird\Product_Service::getAll
	 */
	public function testGetAll() {
		$products = $this->object->getAll();
		$this->assertInstanceOf('Moneybird\Product_Array', $products);
		$this->assertGreaterThan(0, count($products), 'No products found');
	}

}

?>
