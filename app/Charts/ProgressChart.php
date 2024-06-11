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
            'plugins' => '{legend: {labels: {color: "rgba(156, 163, 175,1)"}}}',
            'maintainAspectRatio' => false,
            'fill' => true,
            'responsive' => true,
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Uhrzeit',
                        'color' => "rgba(156, 163, 175,1)",
                    ],
                    'grid' => [
                        'color' => "rgba(156, 163, 175,1)",
                    ],
                    'ticks' => [
                        'color' => "rgba(156, 163, 175,1)",
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Anzahl Zöpfe',
                        'color' => "rgba(156, 163, 175,1)",
                    ],
                    'grid' => [
                        'color' => "rgba(156, 163, 175,1)",
                    ],
                    'ticks' => [
                        'color' => "rgba(156, 163, 175,1)",
                    ],
                ],
            ],
        ]);
    }
}
