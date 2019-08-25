<?php

namespace Davaxi\Sparkline;

/**
 * Trait DataTrait.
 */
trait DataTrait
{
    /**
     * @var int
     *          Base of value
     */
    protected $base;

    /**
     * @var int
     *          Original value of chart
     */
    protected $originValue = 0;

    /**
     * @var array
     */
    protected $data = [
        [0, 0],
    ];

    /**
     * @param $base
     * Set base for values
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * @param float $originValue
     *                           Set origin value of chart
     */
    public function setOriginValue($originValue)
    {
        $this->originValue = $originValue;
    }

    /**
     * @param array $data,...
     */
    public function setData()
    {
        $allSeries = func_get_args();
        if (empty($allSeries)) {
            return;
        }

        $this->data = [];
        foreach ($allSeries as $data) {
            $data = array_values($data);
            $count = count($data);
            if (!$count) {
                $this->data[] = [0, 0];

                return;
            }
            if ($count < static::MIN_DATA_LENGTH) {
                $this->data[] = array_fill(0, 2, $data[0]);

                return;
            }
            $this->data[] = $data;
        }
    }

    /**
     * @return int
     */
    public function getSeriesCount()
    {
        return count($this->data);
    }

    /**
     * @return array
     */
    public function getNormalizedData($seriesIndex = 0)
    {
        $data = $this->data[$seriesIndex];
        foreach ($data as $i => $value) {
            $data[$i] = max(0, $value - $this->originValue);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getData($seriesIndex = 0)
    {
        return $this->data[$seriesIndex];
    }

    /**
     * @return int
     */
    public function getCount($seriesIndex = 0)
    {
        return count($this->data[$seriesIndex]);
    }

    /**
     * @return array
     */
    protected function getMaxValueWithIndex($seriesIndex = 0)
    {
        $max = max($this->data[$seriesIndex]);
        $maxKeys = array_keys($this->data[$seriesIndex], $max);
        $maxIndex = end($maxKeys);
        if ($this->base) {
            $max = $this->base;
        }

        return [$maxIndex, $max];
    }

    /**
     * @return float
     */
    protected function getMaxValue($seriesIndex = 0)
    {
        if ($this->base) {
            return $this->base;
        }

        return max($this->data[$seriesIndex]);
    }

    /**
     * @return array
     */
    protected function getMinValueWithIndex($seriesIndex = 0)
    {
        $min = min($this->data[$seriesIndex]);
        $minKey = array_keys($this->data[$seriesIndex], $min);
        $minIndex = end($minKey);

        return [$minIndex, $min];
    }

    /**
     * @return array
     */
    protected function getExtremeValues()
    {
        list($minIndex, $min) = $this->getMinValueWithIndex();
        list($maxIndex, $max) = $this->getMaxValueWithIndex();

        return [$minIndex, $min, $maxIndex, $max];
    }
}
