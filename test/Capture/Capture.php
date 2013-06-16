<?php
require_once(__DIR__ . '/../../src/Capture/Capture.php');
class Test_Capture extends PHPUnit_Framework_TestCase
{
	public function data()
	{
		return array(
			array(
				array('http://www.yahoo.co.jp/', 'http://phantomjs.org/')
			),
		);
	}

	/**
	 * @dataProvider data
	 */
	public function test__construct(array $urls)
	{
		$this->assertInstanceOf('Capture', $capture = new Capture());
		$url = new ReflectionProperty($capture, 'url');
		$url->setAccessible(true);
		$this->assertNull($url->getValue($capture));

		$bin = new ReflectionProperty($capture, 'bin');
		$bin->setAccessible(true);
		$this->assertNotNull($bin->getValue($capture));

		$this->assertInstanceOf('Capture', $capture = new Capture($urls[0]));
		$url = new ReflectionProperty($capture, 'url');
		$url->setAccessible(true);
		$this->assertSame($urls[0], $url->getValue($capture));

		$this->setExpectedException('InvalidArgumentException');
		new Capture('URLでない文字列');
	}

	/**
	 * @dataProvider data
	 */
	public function test_set_url(array $urls)
	{
		$capture = new Capture();

		$url = new ReflectionProperty($capture, 'url');
		$url->setAccessible(true);

		$this->assertNull($url->getValue($capture));

		$capture->set_url($urls[0]);

		$this->assertEquals($urls[0], $url->getValue($capture));

		$this->setExpectedException('InvalidArgumentException');
		$capture->set_url('URL形式じゃない文字列');
	}

	public function test_set_screen_size()
	{
		$capture = new Capture();

		$screen_width = new ReflectionProperty($capture, 'screen_width');
		$screen_height = new ReflectionProperty($capture, 'screen_height');
		$screen_width->setAccessible(true);
		$screen_height->setAccessible(true);

		$this->assertInternalType('int', $screen_width->getValue($capture));
		$this->assertInternalType('int', $screen_height->getValue($capture));

		$this->assertInstanceOf('Capture', $capture->set_screen_size(9999, 9999));

		$this->assertSame(9999, $screen_width->getValue($capture));
		$this->assertSame(9999, $screen_height->getValue($capture));

		$this->assertInstanceOf('Capture', $capture->set_screen_size(0, 0));

		$this->assertSame(0, $screen_width->getValue($capture));
		$this->assertSame(0, $screen_height->getValue($capture));

		$this->assertInstanceOf('Capture', $capture->set_screen_size('99999', '99999'));

		$this->assertSame(99999, $screen_width->getValue($capture));
		$this->assertSame(99999, $screen_height->getValue($capture));

		$this->setExpectedException('InvalidArgumentException');
		$capture->set_screen_size('あああ', 'いいい');
	}

	public function test_set_screen_width()
	{
		$capture = new Capture();

		$screen_width = new ReflectionProperty($capture, 'screen_width');
		$screen_width->setAccessible(true);
		$this->assertInternalType('int', $screen_width->getValue($capture));

		$this->assertInstanceOf('Capture', $capture->set_screen_width(9999));
		$this->assertSame(9999, $screen_width->getValue($capture));

		$this->assertInstanceOf('Capture', $capture->set_screen_width('99999'));
		$this->assertSame(99999, $screen_width->getValue($capture));

		$this->setExpectedException('InvalidArgumentException');
		$capture->set_screen_width('数字じゃないやつ');
	}
	
	public function test_set_screen_height()
	{
		$capture = new Capture();
		$screen_height = new ReflectionProperty($capture, 'screen_height');
		$screen_height->setAccessible(true);
		$this->assertInternalType('int', $screen_height->getValue($capture));

		$this->assertInstanceOf('Capture', $capture->set_screen_height(9999));
		$this->assertSame(9999, $screen_height->getValue($capture));

		$this->assertInstanceOf('Capture', $capture->set_screen_height('99999'));
		$this->assertSame(99999, $screen_height->getValue($capture));

		$this->setExpectedException('InvalidArgumentException');
		$capture->set_screen_height('数字じゃないやつ');
	}

	/**
	 * @dataProvider data
	 */
	public function test_save($urls)
	{
		$capture = new Capture();
		$capture->set_url($urls[0]);
		$this->assertFileExists($capture->save());

		$path = __DIR__ . '/../..//tmp/test.png';
		if(file_exists($path)){
			unlink($path);
		}
		$this->assertEquals($path, $capture->save($path));
		$this->assertFileExists($path);
	}
}