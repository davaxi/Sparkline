<?php

namespace Davaxi;

/**
 * Class Sparkline.
 */
class Sparkline
{
    const MIN_DATA_LENGTH = 2;
    const FORMAT_DIMENSION = 2;
    const HEXADECIMAL_ALIAS_LENGTH = 3;
    /**
     * @var int
     *          Base of value
     */
    protected $base;

    /**
     * @var int
     *          Recommended: 50 < 800
     */
    protected $width = 80;

    /**
     * @var int
     *          Recommended: 20 < 800
     */
    protected $height = 20;

    /**
     * @var int
     */
    protected $padding = 0;

    /**
     * @var int
     */
    protected $topOffset = 0;

    /**
     * @var array (rgb)
     *            Default: #ffffff
     */
    protected $backgroundColor = [255, 255, 255];

    /**
     * @var array (rgb)
     *            Default: #1388db
     */
    protected $lineColor = [19, 136, 219];

    /**
     * @var array (rgb)
     *            Default: #e6f2fa
     */
    protected $fillColor = [230, 242, 250];

    /**
     * @var array (rgb)
     */
    protected $minimumColor;

    /**
     * @var array (rgb)
     */
    protected $maximumColor;

    /**
     * @var array (rgb)
     */
    protected $lastPointColor;

    /**
     * @var float (px)
     *            Default: 5px
     */
    protected $dotRadius = 5;

    /**
     * @var float (px)
     *            Default: 1.75px
     */
    protected $lineThickness = 1.75;

    /**
     * @var int
     */
    protected $ratioComputing = 4;

    /**
     * @var array
     */
    protected $data = [0, 0];

    /**
     * @var string
     *             ex: QUERY_STRING if dedicated url
     */
    protected $eTag;

    /**
     * @var int
     */
    protected $expire;

    /**
     * @var string
     */
    protected $filename = 'sparkline';

    /**
     * @var resource
     */
    protected $file;

    /**
     * @var array
     */
    protected $server = [];

    /**
     * Sparkline constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!extension_loaded('gd')) {
            throw new \InvalidArgumentException('GD extension is not installed');
        }
    }

    /**
     * @param string $eTag
     */
    public function setETag($eTag)
    {
        if (null === $eTag) {
            $this->eTag = null;

            return;
        }
        $this->eTag = md5($eTag);
    }

    /**
     * @param string $format (Width x Height)
     */
    public function setFormat($format)
    {
        $values = explode('x', $format);
        if (count($values) !== static::FORMAT_DIMENSION) {
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
     * @param int $padding
     */
    public function setPadding($padding)
    {
        $this->padding = (int)$padding;
    }

    /**
     * @param int $topOffset
     */
    public function setTopOffset($topOffset)
    {
        $this->topOffset = (int)$topOffset;
    }

    /**
     * @param string $filename
     *                         Without extension
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param string|int $expire
     *                           time format or string format
     */
    public function setExpire($expire)
    {
        if (null === $expire) {
            $this->expire = null;

            return;
        }
        if (is_numeric($expire)) {
            $this->expire = $expire;

            return;
        }
        $this->expire = strtotime($expire);
    }

    /**
     * @param $base
     * Set base for values
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * Set background to transparent.
     */
    public function deactivateBackgroundColor()
    {
        $this->backgroundColor = null;
    }

    /**
     * @param string $color (hexadecimal)
     */
    public function setBackgroundColorHex($color)
    {
        list($red, $green, $blue) = $this->colorHexToRGB($color);
        $this->setBackgroundColorRGB($red, $green, $blue);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setBackgroundColorRGB($red, $green, $blue)
    {
        $this->backgroundColor = [$red, $green, $blue];
    }

    /**
     * @param string $color (hexadecimal)
     */
    public function setLineColorHex($color)
    {
        list($red, $green, $blue) = $this->colorHexToRGB($color);
        $this->setLineColorRGB($red, $green, $blue);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setLineColorRGB($red, $green, $blue)
    {
        $this->lineColor= [$red, $green, $blue];
    }

    /**
     * @param float $thickness (in px)
     */
    public function setLineThickness($thickness)
    {
        $this->lineThickness = (float)$thickness;
    }

    /**
     * Set fill color to transparent.
     */
    public function deactivateFillColor()
    {
        $this->fillColor = null;
    }

    /**
     * @param string $color (hexadecimal)
     */
    public function setFillColorHex($color)
    {
        list($red, $green, $blue) = $this->colorHexToRGB($color);
        $this->setFillColorRGB($red, $green, $blue);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setFillColorRGB($red, $green, $blue)
    {
        $this->fillColor = [$red, $green, $blue];
    }

    /**
     * @param float $dotRadius
     */
    public function setDotRadius($dotRadius)
    {
        $this->dotRadius = $dotRadius;
    }

    /**
     * @param string $color (hexadecimal)
     */
    public function setMinimumColorHex($color)
    {
        list($red, $green, $blue) = $this->colorHexToRGB($color);
        $this->setMinimumColorRGB($red, $green, $blue);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setMinimumColorRGB($red, $green, $blue)
    {
        $this->minimumColor = [$red, $green, $blue];
    }

    /**
     * @param string $color (hexadecimal)
     */
    public function setMaximumColorHex($color)
    {
        list($red, $green, $blue) = $this->colorHexToRGB($color);
        $this->setMaximumColorRGB($red, $green, $blue);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setMaximumColorRGB($red, $green, $blue)
    {
        $this->maximumColor = [$red, $green, $blue];
    }

    /**
     * @param string $color (hexadecimal)
     */
    public function setLastPointColorHex($color)
    {
        list($red, $green, $blue) = $this->colorHexToRGB($color);
        $this->setLastPointColorRGB($red, $green, $blue);
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setLastPointColorRGB($red, $green, $blue)
    {
        $this->lastPointColor = [$red, $green, $blue];
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $data = array_values($data);
        $count = count($data);
        if (!$count) {
            $this->data = [0, 0];

            return;
        }
        if ($count < static::MIN_DATA_LENGTH) {
            $this->data = array_fill(0, 2, $data[0]);

            return;
        }
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return resource
     */
    public function generate()
    {
        $width = $this->width * $this->ratioComputing;
        $height = $this->height * $this->ratioComputing;
        $padding = $this->padding * $this->ratioComputing;
        $lineThickness = $this->lineThickness * $this->ratioComputing;
        $count = count($this->data);
        $step = $width / ($count - 1);
        $max = max($this->data);
        $maxKeys = array_keys($this->data, $max);
        $maxIndex = end($maxKeys);
        $min = min($this->data);
        $minKey = array_keys($this->data, $min);
        $minIndex = end($minKey);
        if ($this->base) {
            $max = $this->base;
        }
        $picture = imagecreatetruecolor($width + 2 * $padding, $height + 2 * $padding);

        $backgroundColor = imagecolorallocatealpha($picture, 0, 0, 0, 127);
        if ($this->backgroundColor) {
            $backgroundColor = imagecolorallocate(
                $picture,
                $this->backgroundColor[0],
                $this->backgroundColor[1],
                $this->backgroundColor[2]
            );
        }
        $lineColor = imagecolorallocate($picture, $this->lineColor[0], $this->lineColor[1], $this->lineColor[2]);

        imagesavealpha($picture, true);
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
                $value = round($value / $max * ($height-$this->topOffset*$this->ratioComputing));
            }
            $this->data[$i] = max($minHeight, min($value, $maxHeight));
        }

        $pictureX1 = $pictureX2 = $padding;
        $pictureY1 = $height + $padding - $this->data[0];

        $line = [];

        $polygon = [];
        // Initialize
        $polygon[] = $padding;
        $polygon[] = $height + $padding;
        // First element
        $polygon[] = $pictureX1;
        $polygon[] = $pictureY1;
        for ($i = 1; $i < $count; ++$i) {
            $pictureX2 = $pictureX1 + $step;
            $pictureY2 = $height + $padding - $this->data[$i];

            $line[] = [$pictureX1, $pictureY1, $pictureX2, $pictureY2];

            $polygon[] = $pictureX2;
            $polygon[] = $pictureY2;

            $pictureX1 = $pictureX2;
            $pictureY1 = $pictureY2;
        }
        // Last
        $polygon[] = $pictureX2;
        $polygon[] = $height + $padding;

        if ($this->fillColor) {
            $fillColor = imagecolorallocate($picture, $this->fillColor[0], $this->fillColor[1], $this->fillColor[2]);
            imagefilledpolygon($picture, $polygon, $count + 2, $fillColor);
        }

        foreach ($line as $i => $coordinates) {
            list($pictureX1, $pictureY1, $pictureX2, $pictureY2) = $coordinates;
            imageline($picture, $pictureX1, $pictureY1, $pictureX2, $pictureY2, $lineColor);
        }
        if ($min !== $max) {
            if (isset($this->minimumColor) && isset($this->dotRadius)) {
                $minimumColor = imagecolorallocate(
                    $picture,
                    $this->minimumColor[0],
                    $this->minimumColor[1],
                    $this->minimumColor[2]
                );
                imagefilledellipse(
                    $picture,
                    $minIndex * $step + $padding,
                    $height - $this->data[$minIndex] + $padding,
                    2 * $this->dotRadius * $this->ratioComputing,
                    2 * $this->dotRadius * $this->ratioComputing,
                    $minimumColor
                );
            }
            if (isset($this->maximumColor) && isset($this->dotRadius)) {
                $maximumColor = imagecolorallocate(
                    $picture,
                    $this->maximumColor[0],
                    $this->maximumColor[1],
                    $this->maximumColor[2]
                );
                imagefilledellipse(
                    $picture,
                    $maxIndex * $step + $padding,
                    $height - $this->data[$maxIndex] + $padding,
                    2 * $this->dotRadius * $this->ratioComputing,
                    2 * $this->dotRadius * $this->ratioComputing,
                    $maximumColor
                );
            }
        }
        if (isset($this->lastPointColor) && isset($this->dotRadius)) {
            $lastPointColor = imagecolorallocate(
                $picture,
                $this->lastPointColor[0],
                $this->lastPointColor[1],
                $this->lastPointColor[2]
            );
            imagefilledellipse(
                $picture,
                ($count - 1) * $step + $padding,
                $height - $this->data[$count - 1] + $padding,
                2 * $this->dotRadius * $this->ratioComputing,
                2 * $this->dotRadius * $this->ratioComputing,
                $lastPointColor
            );
        }
        $sparkline = imagecreatetruecolor($this->width + 2 * $this->padding, $this->height + 2 * $this->padding);
        imagealphablending($sparkline, false);
        imagecopyresampled(
            $sparkline,
            $picture,
            0,
            0,
            0,
            0,
            $this->width + 2 * $this->padding,
            $this->height + 2 * $this->padding,
            $width + $this->ratioComputing * 2 * $this->padding,
            $height + $this->ratioComputing * 2 * $this->padding
        );
        imagesavealpha($sparkline, true);
        imagedestroy($picture);
        $this->file = $sparkline;
    }

    /**
     * @param array $server
     */
    public function setServer(array $server)
    {
        $this->server = $server;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function getServerValue($key)
    {
        if (isset($this->server[$key])) {
            return $this->server[$key];
        }

        return null;
    }

    public function display()
    {
        if (!$this->file) {
            $this->generate();
        }
        $httpIfNoneMatch = $this->getServerValue('HTTP_IF_NONE_MATCH');
        if ($this->eTag && $httpIfNoneMatch) {
            if ($httpIfNoneMatch === $this->eTag) {
                $serverProtocol = $this->getServerValue('SERVER_PROTOCOL');
                header($serverProtocol . ' 304 Not Modified', true, 304);

                return;
            }
        }

        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="' . $this->filename . '.png"');
        header('Accept-Ranges: none');
        if ($this->eTag) {
            header('ETag: ' . $this->eTag);
        }
        if (null !== $this->expire) {
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
        $buffer = ob_get_contents();
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
     *
     * @return array (r,g,b)
     */
    protected function colorHexToRGB($color)
    {
        if (!$this->checkColorHex($color)) {
            throw new \InvalidArgumentException('Invalid hexadecimal value ' . $color);
        }

        $color = mb_strtolower($color);
        $color = ltrim($color, '#');
        if (mb_strlen($color) === static::HEXADECIMAL_ALIAS_LENGTH) {
            $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
        }
        $color = hexdec($color);

        return [
            0xFF & ($color >> 0x10), // Red
            0xFF & ($color >> 0x8), // Green
            0xFF & $color, // Blue
        ];
    }

    protected static function checkColorHex($color)
    {
        return preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $color);
    }
}
