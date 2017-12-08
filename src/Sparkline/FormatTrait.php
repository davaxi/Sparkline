<?php

namespace Davaxi\Sparkline;

/**
 * Trait FormatTrait.
 */
trait FormatTrait
{
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
    protected $ratioComputing = 4;

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
     * @return int
     */
    protected function getNormalizedHeight()
    {
        return $this->height * $this->ratioComputing;
    }

    /**
     * @return int
     */
    protected function getNormalizedWidth()
    {
        return $this->width * $this->ratioComputing;
    }

    /**
     * @return array
     */
    protected function getNormalizedSize()
    {
        return [
            $this->getNormalizedWidth(),
            $this->getNormalizedHeight(),
        ];
    }

    /**
     * @param $width
     * @param $count
     *
     * @return float|int
     */
    protected function getStepWidth($width, $count)
    {
        return $width / ($count - 1);
    }

    /**
     * @param array $data
     * @param $height
     * @param $max
     *
     * @return array
     */
    protected function getDataForChartElements(array $data, $height, $max)
    {
        $minHeight = 1 * $this->ratioComputing;
        $maxHeight = $height - $minHeight;
        foreach ($data as $i => $value) {
            $value = (int)$value;
            if ($value <= 0) {
                $value = 0;
            }
            if ($value > 0) {
                $value = round($value / $max * $height);
            }
            $data[$i] = max($minHeight, min($value, $maxHeight));
        }

        return $data;
    }

    /**
     * @param array $data
     * @param $max
     * @param $step
     *
     * @return array
     */
    protected function getChartElements(array $data, $max, $step)
    {
        $count = count($data);
        $height = $this->getNormalizedHeight();
        $data = $this->getDataForChartElements($data, $height, $max);

        $pictureX1 = $pictureX2 = 0;
        $pictureY1 = $height - $data[0];

        $polygon = [];
        $line = [];

        // Initialize
        $polygon[] = 0;
        $polygon[] = $height + 50;
        // First element
        $polygon[] = $pictureX1;
        $polygon[] = $pictureY1;
        for ($i = 1; $i < $count; ++$i) {
            $pictureX2 = $pictureX1 + $step;
            $pictureY2 = $height - $data[$i];

            $line[] = [$pictureX1, $pictureY1, $pictureX2, $pictureY2];

            $polygon[] = $pictureX2;
            $polygon[] = $pictureY2;

            $pictureX1 = $pictureX2;
            $pictureY1 = $pictureY2;
        }
        // Last
        $polygon[] = $pictureX2;
        $polygon[] = $height + 50;

        return [$polygon, $line];
    }
}
