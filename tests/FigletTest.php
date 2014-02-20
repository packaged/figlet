<?php

class FigletTest extends PHPUnit_Framework_TestCase
{
  public function testNoDefaultFont()
  {
    $figlet = new \Packaged\Figlet\Figlet();
    $this->assertFalse($figlet->isFontLoaded());

    //Check loads default font
    $this->assertEquals($this->getStandardA(), $figlet->getCharacter("a"));

    $figlet->loadFont('mini');
    $this->assertEquals('mini', $figlet->currentFont());
    $this->assertTrue($figlet->isFontLoaded());
    $this->assertEquals($this->getSpeedA(), $figlet->getCharacter("a"));
  }

  public function testLoadsFontOnConstruct()
  {
    $figlet = new \Packaged\Figlet\Figlet('mini');
    $this->assertTrue($figlet->isFontLoaded());
    $this->assertEquals('mini', $figlet->currentFont());
    $this->assertEquals($this->getSpeedA(), $figlet->getCharacter("a"));
  }

  public function testLoadFileSetsCurrentFont()
  {
    $figlet = new \Packaged\Figlet\Figlet();
    $font   = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src';
    $font .= DIRECTORY_SEPARATOR . '.fonts' . DIRECTORY_SEPARATOR;
    $font .= 'speed.flf';
    $figlet->loadFontFromPath($font);
    $this->assertEquals($font, $figlet->currentFont());
  }

  public function testCorruptFile()
  {
    $figlet   = new \Packaged\Figlet\Figlet();
    $fontFile = __DIR__ . DIRECTORY_SEPARATOR;
    $fontFile .= 'res' . DIRECTORY_SEPARATOR . 'corrupt.file';
    $this->setExpectedException(
      "Exception",
      "Invalid figlet font file provided",
      500
    );
    $figlet->loadFontFromPath($fontFile);
  }

  public function testInvalidFont()
  {
    $figlet   = new \Packaged\Figlet\Figlet();
    $fontFile = 'fwlkhfw';

    $this->setExpectedException(
      "Exception",
      "Could not load figlet font '" . $fontFile . "'",
      404
    );

    $figlet->loadFontFromPath($fontFile);
  }

  public function testStaticCreate()
  {
    $this->assertEquals(
      $this->getStandardHello(),
      \Packaged\Figlet\Figlet::create('Hello')
    );
    $this->assertEquals(
      $this->getMiniHello(),
      \Packaged\Figlet\Figlet::create('Hello', 'mini')
    );
  }

  public function testRender()
  {
    $figlet = new \Packaged\Figlet\Figlet();
    $this->assertEquals(
      $this->getStandardHello(),
      $figlet->render('Hello')
    );
    $figlet->loadFont('mini');
    $this->assertEquals(
      $this->getMiniHello(),
      $figlet->render('Hello')
    );
  }

  public function getSpeedA()
  {
    return array(
      '     ',
      '  _. ',
      ' (_| ',
      '     '
    );
  }

  public function getStandardA()
  {
    return Array
    (
      '        ',
      '   __ _ ',
      '  / _` |',
      ' | (_| |',
      '  \__,_|',
      '        '
    );
  }

  public function getStandardHello()
  {
    return
      '  _   _          _   _         ' . "\n" .
      ' | | | |   ___  | | | |   ___  ' . "\n" .
      ' | |_| |  / _ \ | | | |  / _ \\ ' . "\n" .
      ' |  _  | |  __/ | | | | | (_) |' . "\n" .
      ' |_| |_|  \___| |_| |_|  \___/ ' . "\n" .
      '                               ' . "\n";
  }

  public function getMiniHello()
  {
    return
      '                     ' . "\n" .
      ' |_|   _   |  |   _  ' . "\n" .
      ' | |  (/_  |  |  (_) ' . "\n" .
      '                     ' . "\n";
  }
}
