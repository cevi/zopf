<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class ProgressChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return ProgressChart
     */
    public function __construct()
    {
        parent::__construct();


        return $this->options([
            'maintainAspectRatio' => false,
            'fill'=> true,
            'responsive'=> true,
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false
            ],
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Uhrzeit',
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Anzahl Zöpfe',
                    ],
                ],
            ],
        ]);
    }
}
