<?php

namespace Davaxi;

/**
 * Class Sparkline
 * @package Davaxi
 */
class Sparkline
{

    /**
     * @var int
     * Recommended: 50 < 800
     */
    protected $width = 80;

    /**
     * @var int
     * Recommended: 20 < 800
     */
    protected $height = 20;

    /**
     * @var array (rgb)
     * Default: #ffffff
     */
    protected $backgroundColor = array(255, 255, 255);

    /**
     * @var array (rgb)
     * Default: #1388db
     */
    protected $lineColor = array(19, 136, 219);

    /**
     * @var array (rgb)
     * Default: #e6f2fa
     */
    protected $fillColor = array(230, 242, 250);

    /**
     * @var float (px)
     * Default: 1.75px
     */
    protected $lineThickness = 1.75;

    /**
     * @var int
     */
    protected $ratioComputing = 4;

    /**
     * @var array
     */
    protected $data = array(0, 0);

    /**
     * @var string
     * ex: QUERY_STRING if dedicated url
     */
    protected $ETag = null;

    /**
     * @var int
     */
    protected $expire = null;

    /**
     * @var string
     */
    protected $filename = 'sparkline';

    /**
     * @var resource
     */
    protected $file;

    /**
     * Sparkline constructor.
     */
    public function __construct()
    {
        if (!extension_loaded('gd')) {
            throw new \InvalidArgumentException('GD extension is not installed');
        }
    }

    /**
     * @param string $ETag
     */
    public function setETag($ETag)
    {
        if (is_null($ETag)) {
            $this->ETag = null;
        }
        else {
            $this->ETag = md5($ETag);
        }
    }

    /**
     * @param string $format (Width x Height)
     */
    public function setFormat($format)
    {
        $values = explode('x', $format);
        if (count($values) != 2) {
            throw new \InvalidArgumentException('Invalid format params. Expected string Width x Height');
        }
        $this->setWidth($values[0]);
        $this->setHeight($values[1]);
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = (int)$width;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = (int)$height;
    }

    /**
     * @param string $filename
     * Without extension
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param string|int $expire
     * time format or string format
     */
    public function setExpire($expire)
    {
        if (is_null($expire)) {
            $this->expire = null;
        }
        if (is_numeric($expire)) {
            $this->expire = $expire;
        }
        else {
            $this->expire = strtotime($expire);
        }
    }

    /**
     * @param string $color (hexadecimal)
     */
    public function setBackgroundColorHex($color)
    {
        list($red, $green, $blue) = Sparkline::ColorHexToRGB($color);
        $this->setBackgroundColorRGB($red, $green, $blue);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setBackgroundColorRGB($red, $green, $blue)
    {
        $this->backgroundColor = array($red, $green, $blue);
    }

    /**
     * @param string $color (hexadecimal)
     */
    public function setLineColorHex($color)
    {
        list($red, $green, $blue) = Sparkline::ColorHexToRGB($color);
        $this->setLineColorRGB($red, $green, $blue);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setLineColorRGB($red, $green, $blue)
    {
        $this->lineColor= array($red, $green, $blue);
    }

    /**
     * @param float $thickness (in px)
     */
    public function setLineThickness($thickness)
    {
        $this->lineThickness = (float)$thickness;
    }

    /**
     * @param string $color (hexadecimal)
     */
    public function setFillColorHex($color)
    {
        list($red, $green, $blue) = Sparkline::ColorHexToRGB($color);
        $this->setFillColorRGB($red, $green, $blue);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setFillColorRGB($red, $green, $blue)
    {
        $this->fillColor = array($red, $green, $blue);
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $data = array_values($data);
        $count = count($data);
        if (!$count) {
            $this->data = array(0, 0);
        }
        else if ($count < 2) {
            $this->data = array_fill(0, 2, $data[0]);
        }
        else {
            $this->data = $data;
        }
    }

    /**
     * @return resource
     */
    public function generate()
    {
        $width = $this->width * $this->ratioComputing;
        $height = $this->height * $this->ratioComputing;
        $lineThickness = $this->lineThickness * $this->ratioComputing;
        $count = count($this->data);
        $step = $width / ($count - 1);
        $max = max($this->data);

        $picture = imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($picture, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
        $fillColor = imagecolorallocate($picture, $this->fillColor[0], $this->fillColor[1], $this->fillColor[2]);
        $lineColor = imagecolorallocate($picture, $this->lineColor[0], $this->lineColor[1], $this->lineColor[2]);

        imagefill($picture, 0, 0, $backgroundColor);
        imagesetthickness($picture, $lineThickness);

        $minHeight = 1 * $this->ratioComputing;
        $maxHeight = $height - $minHeight;
        foreach ($this->data as $i => $value) {
            $value = (int)$value;
            if ($value <= 0) {
                $value = 0;
            }
            if ($value > 0) {
                $value = round($value / $max * $height);
            }
            $this->data[$i] = max($minHeight, min($value, $maxHeight));
        }

        $x1 = $x2 = 0;
        $y1 = $height - $this->data[0];

        $line = array();

        $polygon = array();
        // Initialize
        $polygon[] = 0;
        $polygon[] = $height + 50;
        // First element
        $polygon[] = $x1;
        $polygon[] = $y1;
        for ($i = 1; $i < $count; $i++) {
            $x2 = $x1 + $step;
            $y2 = $height - $this->data[$i];

            $line[] = array($x1, $y1, $x2, $y2);

            $polygon[] = $x2;
            $polygon[] = $y2;

            $x1 = $x2;
            $y1 = $y2;
        }
        // Last
        $polygon[] = $x2;
        $polygon[] = $height + 50;

        imagefilledpolygon($picture, $polygon, $count + 2, $fillColor);

        foreach ($line as $i => $coordinates) {
            list($x1, $y1, $x2, $y2) = $coordinates;
            imageline($picture, $x1, $y1, $x2, $y2, $lineColor);
        }
        $sparkline = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled($sparkline, $picture, 0, 0, 0, 0, $this->width, $this->height, $width, $height);
        imagedestroy($picture);
        $this->file = $sparkline;
    }

    public function display()
    {
        if (!$this->file) {
            $this->generate();
        }
        if ($this->ETag && isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if ($_SERVER['HTTP_IF_NONE_MATCH'] == $this->ETag) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304);
                exit();
            }
        }

        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="' . $this->filename . '.png"');
        header('Accept-Ranges: none');
        if ($this->ETag) {
            header('ETag: ' . $this->ETag);
        }
        if (!is_null($this->expire)) {
            header('Expires: ' . gmdate('D, d M Y H:i:s T', $this->expire));
        }
        imagepng($this->file);
    }

    public function save($savePath)
    {
        if (!$this->file) {
            $this->generate();
        }
        imagepng($this->file, $savePath);
    }
    
    /**
     * @return string
     */
    public function toBase64()
    {
        if (!$this->file) {
            $this->generate();
        }
        ob_start();
        imagepng($this->file);
        $buffer = ob_get_clean();
        if (ob_get_length()) {
            ob_end_clean();
        }
        return base64_encode($buffer);
    }

    public function destroy()
    {
        if ($this->file) {
            imagedestroy($this->file);
        }
        $this->file = null;
    }


    /**
     * @param string $color (hexadecimal)
     * @exceptions \InvalidArgumentException
     * @return array (r,g,b)
     */
    protected static function ColorHexToRGB($color)
    {
        if (!Sparkline::checkColorHex($color)) {
            throw new \InvalidArgumentException('Invalid hexadecimal value ' . $color);
        }

        $color = strtolower($color);
        $color = ltrim($color, '#');
        if (strlen($color) == 3) {
            $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
        }
        $color = hexdec($color);
        return array(
            0xFF & ($color >> 0x10), // Red
            0xFF & ($color >> 0x8), // Green
            0xFF & $color // Blue
        );
    }

    protected static function checkColorHex($color)
    {
        return preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $color);
    }

}
