<?php

use Davaxi\Sparkline as Sparkline;

class SparklineMockup extends Sparkline
{
    public function getAttribute($attribute)
    {
        return $this->$attribute;
    }
}

/**
 * Class SparklineTest.
 */
class SparklineTest extends SparklinePHPUnit
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
        $eTag = $this->sparkline->getAttribute('eTag');
        $this->assertNull($eTag);

        $this->sparkline->setETag('random hash');
        $eTag = $this->sparkline->getAttribute('eTag');
        $this->assertNotNull($eTag);

        $this->sparkline->setETag(null);
        $eTag = $this->sparkline->getAttribute('eTag');
        $this->assertNull($eTag);
    }

    public function testSetWidth()
    {
        $this->sparkline->setWidth(130);
        $width = $this->sparkline->getAttribute('width');
        $this->assertEquals(130, $width);
    }

    public function testServer()
    {
        $expected = ['test' => 1];
        $this->sparkline->setServer($expected);
        $value = $this->sparkline->getAttribute('server');
        $this->assertEquals($expected, $value);
    }

    public function testGetServerValue()
    {
        $server = ['test' => 1];
        $this->sparkline->setServer($server);
        $value = $this->sparkline->getServerValue('test');
        $this->assertEquals(1, $value);

        $value = $this->sparkline->getServerValue('invalid key');
        $this->assertNull($value);
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

        $this->sparkline->setExpire(null);
        $expire = $this->sparkline->getAttribute('expire');
        $this->assertNull($expire);
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
        $this->assertEquals([15, 53, 75], $color);

        $this->sparkline->setBackgroundColorHex('#666');
        $color = $this->sparkline->getAttribute('backgroundColor');
        $this->assertEquals([102, 102, 102], $color);
    }

    public function testSetBackgroundColorRGB()
    {
        $this->sparkline->setBackgroundColorRGB(123, 233, 199);
        $color = $this->sparkline->getAttribute('backgroundColor');
        $this->assertEquals([123, 233, 199], $color);
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
        $this->assertEquals([15, 53, 75], $color);

        $this->sparkline->setLineColorHex('#666');
        $color = $this->sparkline->getAttribute('lineColor');
        $this->assertEquals([102, 102, 102], $color);
    }

    public function testSetLineColorRGB()
    {
        $this->sparkline->setLineColorRGB(123, 233, 199);
        $color = $this->sparkline->getAttribute('lineColor');
        $this->assertEquals([123, 233, 199], $color);
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
        $this->assertEquals([15, 53, 75], $color);

        $this->sparkline->setFillColorHex('#666');
        $color = $this->sparkline->getAttribute('fillColor');
        $this->assertEquals([102, 102, 102], $color);
    }

    public function testSetFillColorRGB()
    {
        $this->sparkline->setFillColorRGB(123, 233, 199);
        $color = $this->sparkline->getAttribute('fillColor');
        $this->assertEquals([123, 233, 199], $color);
    }

    public function testSetLineThickness()
    {
        $this->sparkline->setLineThickness(2.5);
        $lineThickness = $this->sparkline->getAttribute('lineThickness');
        $this->assertEquals(2.5, $lineThickness);
    }

    public function testSetData()
    {
        $this->sparkline->setData([]);
        $data = $this->sparkline->getAttribute('data');
        $this->assertEquals([0,0], $data);

        $this->sparkline->setData([1]);
        $data = $this->sparkline->getAttribute('data');
        $this->assertEquals([1,1], $data);

        $this->sparkline->setData([1, 2]);
        $data = $this->sparkline->getAttribute('data');
        $this->assertEquals([1,2], $data);

        $this->sparkline->setData([1, 3, 5]);
        $data = $this->sparkline->getAttribute('data');
        $this->assertEquals([1, 3, 5], $data);
    }

    /**
     * @runInSeparateProcess
     */
    public function testDisplayNotModified()
    {
        $eTag = uniqid();
        $this->sparkline->setETag($eTag);
        $this->sparkline->setServer(
            [
                'SERVER_PROTOCOL' => 'HTTP/1.0',
                'HTTP_IF_NONE_MATCH' => $this->sparkline->getAttribute('eTag'),
            ]
        );
        $this->sparkline->display();
    }

    /**
     * @runInSeparateProcess
     */
    public function testDisplayModified()
    {
        $eTag = uniqid();
        $this->sparkline->setETag('Other Etag');
        $this->sparkline->setExpire('2016-01-01 00:00:00');
        $this->sparkline->setServer(
            [
                'SERVER_PROTOCOL' => 'HTTP/1.0',
                'HTTP_IF_NONE_MATCH' => $eTag,
            ]
        );
        ob_start();
        $this->sparkline->display();
        $picture = ob_get_clean();

        $headers = xdebug_get_headers();
        $this->assertContains('Content-Type: image/png', $headers);
        $this->assertContains('Content-Disposition: inline; filename="sparkline.png"', $headers);
        $this->assertContains('Accept-Ranges: none', $headers);

        $path = __DIR__ . '/data/testGenerate.png';
        $expectedPath = __DIR__ . '/data/testGenerate-mockup.png';

        file_put_contents($path, $picture);
        $md5 = md5_file($path);
        unlink($path);

        $expectedMD5 = md5_file($expectedPath);
        $this->assertEquals($expectedMD5, $md5);
    }

    public function Generate_empty()
    {
        $path = __DIR__ . '/data/testGenerate.png';
        $expectedPath = __DIR__ . '/data/testGenerate-mockup.png';

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
        $path = __DIR__ . '/data/testGenerate2.png';
        $expectedPath = __DIR__ . '/data/testGenerate2-mockup.png';

        $this->sparkline->setData([2,4,5,6,10,7,8,5,7,7,11,8,6,9,11,9,13,14,12,16]);
        $this->sparkline->generate();
        $file = $this->sparkline->getAttribute('file');
        imagepng($file, $path);

        $md5 = md5_file($path);
        $expectedMD5 = md5_file($expectedPath);
        if (getenv('TRAVIS') && phpversion() >= "5.6") {
            $expectedMD5 = '5337e6e4fcd213fe3d55c89df8c00e32';
        }
        $this->assertEquals($expectedMD5, $md5);

        unlink($path);
    }

    public function testToBase64()
    {
        $expectedPath = __DIR__ . '/data/testGenerate-mockup.png';
        $expectedContent = file_get_contents($expectedPath);
        $expectedPathBase64 = base64_encode($expectedContent);

        $value = $this->sparkline->toBase64();
        $this->assertEquals($expectedPathBase64, $value);
    }

    public function testPoints()
    {
        $path = __DIR__ . '/data/testGeneratePoints.png';
        $this->sparkline->setData([-1, 2,4,5,6,10,7,8,5,7,7,11,8,6,9,11,9,13,14,12,16]);
        $this->sparkline->addPoint(4, 4, '#6a737b');
        $this->sparkline->addPoint('first', 3, '#fbb034');
        $this->sparkline->addPoint('last', 3, '#008374');
        $this->sparkline->addPoint('maximum', 5, '#ff4c4c');
        $this->sparkline->addPoint('minimum', 5, '#0099e5');
        $this->sparkline->save($path);

        $this->assertFileExists($path);
        unlink($path);
    }

    public function testMargin()
    {
        $path = __DIR__ . '/data/testGenerateMargin.png';
        $this->sparkline->setFormat('250x250');
        $this->sparkline->setData([-1, 2,4,5,6,10,7,8,5,7,7,11,8,6,9,11,9,13,14,12,16]);
        $this->sparkline->addPoint(4, 4, '#6a737b');
        $this->sparkline->setPadding("10 20 30 40");
        $this->sparkline->save($path);
        $this->assertFileExists($path);
        unlink($path);
    }

    public function testSave()
    {
        $path = __DIR__ . '/data/testGenerateSave.png';
        $this->sparkline->setData([-1, 2,4,5,6,10,7,8,5,7,7,11,8,6,9,11,9,13,14,12,16]);
        $this->sparkline->save($path);

        $this->assertFileExists($path);
        unlink($path);
    }
}
