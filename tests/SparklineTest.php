<?php

use Davaxi\Sparkline as Sparkline;

class SparklineMockup extends Sparkline
{
    public function getAttribute($attribute)
    {
        return $this->$attribute;
    }
}

class SparklineTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SparklineMockup
     */
    protected $sparkline;

    public function setUp()
    {
        parent::setUp();
        $this->sparkline = new SparklineMockup();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->sparkline->destroy();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetFormat_invalid()
    {
        $this->sparkline->setFormat('invalid format');
    }

    public function testSetFormat()
    {
        $this->sparkline->setFormat('120x280');
        $width = $this->sparkline->getAttribute('width');
        $this->assertEquals(120, $width);

        $height = $this->sparkline->getAttribute('height');
        $this->assertEquals(280, $height);
    }

    public function testSetETag()
    {
        $ETag = $this->sparkline->getAttribute('ETag');
        $this->assertNull($ETag);

        $this->sparkline->setETag('random hash');
        $ETag = $this->sparkline->getAttribute('ETag');
        $this->assertNotNull($ETag);

        $this->sparkline->setETag(null);
        $ETag = $this->sparkline->getAttribute('ETag');
        $this->assertNull($ETag);
    }

    public function testSetWidth()
    {
        $this->sparkline->setWidth(130);
        $width = $this->sparkline->getAttribute('width');
        $this->assertEquals(130, $width);
    }

    public function testSetHeight()
    {
        $this->sparkline->setHeight('200');
        $height = $this->sparkline->getAttribute('height');
        $this->assertEquals(200, $height);
    }

    public function testSetFilename()
    {
        $this->sparkline->setFilename('myPrivateName');
        $filename = $this->sparkline->getAttribute('filename');
        $this->assertEquals('myPrivateName', $filename);
    }

    public function testSetExpire()
    {
        $expire = $this->sparkline->getAttribute('expire');
        $this->assertNull($expire);

        $this->sparkline->setExpire(10000);
        $expire = $this->sparkline->getAttribute('expire');
        $this->assertEquals(10000, $expire);

        $this->sparkline->setExpire('2015-09-01 00:00:00');
        $expire = $this->sparkline->getAttribute('expire');
        $this->assertEquals(1441058400, $expire);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetBackgroundColorHex_invalid()
    {
        $this->sparkline->setBackgroundColorHex('invalid hexadecimal color');
    }

    public function testSetBackgroundColorHex()
    {
        $this->sparkline->setBackgroundColorHex('#0f354b');
        $color = $this->sparkline->getAttribute('backgroundColor');
        $this->assertEquals(array(15, 53, 75), $color);
    }

    public function testSetBackgroundColorRGB()
    {
        $this->sparkline->setBackgroundColorRGB(123, 233, 199);
        $color = $this->sparkline->getAttribute('backgroundColor');
        $this->assertEquals(array(123, 233, 199), $color);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetLineColorHex_invalid()
    {
        $this->sparkline->setLineColorHex('invalid hexadecimal color');
    }

    public function testSetLineColorHex()
    {
        $this->sparkline->setLineColorHex('#0f354b');
        $color = $this->sparkline->getAttribute('lineColor');
        $this->assertEquals(array(15, 53, 75), $color);
    }

    public function testSetLineColorRGB()
    {
        $this->sparkline->setLineColorRGB(123, 233, 199);
        $color = $this->sparkline->getAttribute('lineColor');
        $this->assertEquals(array(123, 233, 199), $color);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetFillColorHex_invalid()
    {
        $this->sparkline->setFillColorHex('invalid hexadecimal color');
    }

    public function testSetFillColorHex()
    {
        $this->sparkline->setFillColorHex('#0f354b');
        $color = $this->sparkline->getAttribute('fillColor');
        $this->assertEquals(array(15, 53, 75), $color);
    }

    public function testSetFillColorRGB()
    {
        $this->sparkline->setFillColorRGB(123, 233, 199);
        $color = $this->sparkline->getAttribute('fillColor');
        $this->assertEquals(array(123, 233, 199), $color);
    }

    public function testSetLineThickness()
    {
        $this->sparkline->setLineThickness(2.5);
        $lineThickness = $this->sparkline->getAttribute('lineThickness');
        $this->assertEquals(2.5, $lineThickness);
    }

    public function testSetData()
    {
        $this->sparkline->setData(array());
        $data = $this->sparkline->getAttribute('data');
        $this->assertEquals(array(0,0), $data);

        $this->sparkline->setData(array(1));
        $data = $this->sparkline->getAttribute('data');
        $this->assertEquals(array(1,1), $data);

        $this->sparkline->setData(array(1, 2));
        $data = $this->sparkline->getAttribute('data');
        $this->assertEquals(array(1,2), $data);

        $this->sparkline->setData(array(1, 3, 5));
        $data = $this->sparkline->getAttribute('data');
        $this->assertEquals(array(1, 3, 5), $data);
    }

    public function Generate_empty()
    {
        $path = dirname(__FILE__) . '/data/testGenerate.png';
        $expectedPath = dirname(__FILE__) . '/data/testGenerate-mockup.png';

        $this->sparkline->generate();
        $file = $this->sparkline->getAttribute('file');
        imagepng($file, $path);

        $md5 = md5_file($path);
        $expectedMD5 = md5_file($expectedPath);
        $this->assertEquals($expectedMD5, $md5);

        unlink($path);
    }

    public function testGenerate_data()
    {
        $path = dirname(__FILE__) . '/data/testGenerate2.png';
        $expectedPath = dirname(__FILE__) . '/data/testGenerate2-mockup.png';

        $this->sparkline->setData(array(2,4,5,6,10,7,8,5,7,7,11,8,6,9,11,9,13,14,12,16));
        $this->sparkline->generate();
        $file = $this->sparkline->getAttribute('file');
        imagepng($file, $path);

        $md5 = md5_file($path);
        $expectedMD5 = md5_file($expectedPath);
        $this->assertEquals($expectedMD5, $md5);

        unlink($path);
    }

    public function testSave()
    {
        $path = dirname(__FILE__) . '/data/testGenerate2.png';
        $this->sparkline->setData(array(2,4,5,6,10,7,8,5,7,7,11,8,6,9,11,9,13,14,12,16));
        $this->sparkline->save($path);

        $this->assertFileExists($path);
        unlink($path);
    }
}