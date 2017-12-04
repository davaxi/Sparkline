<?php

namespace Davaxi\Sparkline;

/**
 * Trait StyleTrait.
 */
trait StyleTrait
{
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
