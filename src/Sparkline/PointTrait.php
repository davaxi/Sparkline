<?php

namespace Davaxi\Sparkline;

/**
 * Trait PointTrait.
 */
trait PointTrait
{
    /**
     * @var array
     */
    protected $points = [];

    /**
     * @param $index
     * @param $dotRadius
     * @param $colorHex
     */
    public function addPoint($index, $dotRadius, $colorHex)
    {
        $count = $this->getCount();
        list($minIndex, $min, $maxIndex, $max) = $this->getExtremeValues();

        $mapping = [];
        if ($count > 1) {
            $mapping['first'] = 0;
            $mapping['last'] = $count - 1;
        }
        if ($min !== $max) {
            $mapping['minimum'] = $minIndex;
            $mapping['maximum'] = $maxIndex;
        }
        if (array_key_exists($index, $mapping)) {
            $index = $mapping[$index];
        }
        if (!is_numeric($index)) {
            throw new \InvalidArgumentException('Invalid index : ' . $index);
        }
        if ($index < 0 || $index >= $count) {
            throw new \InvalidArgumentException('Index out of range [0-' . $count - 1 . '] : ' . $index);
        }
        $this->points[] = [
            'index' => $index,
            'radius' => $dotRadius,
            'color' => $this->colorHexToRGB($colorHex),
        ];
    }
}
