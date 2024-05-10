<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class ZopfChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return ZopfChart
     */
    public function __construct()
    {
        parent::__construct();

        return $this->options(['plugins' => '{legend: {labels: {color: "rgba(156, 163, 175,1)"}}}']);
    }
}
