<?php
namespace Glv\Test;

class ExampleTest extends TestCase
{
	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$crawler = $this->client->request('GET', '/');

		$thls->assertTrue($this->client->getResponse()->isOk());
	}
}
