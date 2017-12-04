<?php

namespace Davaxi;

use Davaxi\Sparkline\DataTrait;
use Davaxi\Sparkline\FormatTrait;
use Davaxi\Sparkline\Picture;
use Davaxi\Sparkline\StyleTrait;

/**
 * Class Sparkline.
 */
class Sparkline
{
    use StyleTrait;
    use DataTrait;
    use FormatTrait;

    const MIN_DATA_LENGTH = 2;
    const FORMAT_DIMENSION = 2;
    const HEXADECIMAL_ALIAS_LENGTH = 3;

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

    public function generate()
    {
        $width = $this->getNormalizedWidth();
        $height = $this->getNormalizedHeight();

        $count = $this->getCount();
        $step = $this->getStepWidth($width, $count);

        $max = $this->getMaxValue();
        $maxKeys = array_keys($this->data, $max);
        $maxIndex = end($maxKeys);

        $min = $this->getMinValue();
        $minKey = array_keys($this->data, $min);
        $minIndex = end($minKey);
        if ($this->base) {
            $max = $this->base;
        }
        $this->computeDataForChartElements($height, $max);

        list($polygon, $line) = $this->getChartElements($height, $step, $count);

        $picture = new Picture($width, $height);
        $picture->applyBackground($this->backgroundColor);
        $picture->applyThickness($this->lineThickness * $this->ratioComputing);
        $picture->applyPolygon($polygon, $this->fillColor, $count);
        $picture->applyLine($line, $this->lineColor);

        if ($min !== $max && isset($this->dotRadius)) {
            if (isset($this->minimumColor)) {
                $picture->applyDot(
                    $minIndex * $step,
                    $height - $this->data[$minIndex],
                    $this->dotRadius * $this->ratioComputing,
                    $this->minimumColor
                );
            }
            if (isset($this->maximumColor)) {
                $picture->applyDot(
                    $maxIndex * $step,
                    $height - $this->data[$maxIndex],
                    $this->dotRadius * $this->ratioComputing,
                    $this->maximumColor
                );
            }
        }

        $this->file = $picture->generate($this->width, $this->height);
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
}
