<?php

namespace Xentral\Widgets\Chart;

class HtmlRenderer
{
    /** @var Chart $chart */
    protected $chart;

    /** @var string $title */
    protected $title;

    /** @var int $width */
    protected $width;

    /** @var int $height */
    protected $height;

    /** @var array $attributes */
    protected $attributes;

    /**
     * @param Chart  $chart
     * @param string $title
     * @param int    $width
     * @param int    $height
     * @param array  $attributes Zusätzliche HTML-Attribute als assoziatives Array
     */
    public function __construct(Chart $chart, $title = '', $width = 400, $height = 200, array $attributes = [])
    {
        $this->chart = $chart;
        $this->title = $title;
        $this->width = (int)$width;
        $this->height = (int)$height;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function render()
    {
        return sprintf(
            '<div class="chart-wrapper" %s>%s' .
            '  <div class="chart-content">' .
            '    <canvas data-graph-id="%s" width="%s" height="%s"></canvas>' .
            '    <script type="application/json">%s</script>' .
            '  </div>' .
            '</div>',
            $this->renderAttributes(),
            $this->renderTitle(),
            uniqid(null, false),
            $this->width,
            $this->height,
            $this->chart->toJson()
        );
    }

    /**
     * @return string
     */
    protected function renderAttributes()
    {
        $result = '';
        foreach ($this->attributes as $key => $value) {
            $result .= sprintf(' %s="%s"', $key, $value);
        }

        return trim($result);
    }

    /**
     * @return string
     */
    protected function renderTitle()
    {
        if (empty($this->title)) {
            return '';
        }

        return sprintf('<div class="chart-title">%s</div>', $this->title);
    }
}
