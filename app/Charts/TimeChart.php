<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class TimeChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return TimeChart
     */
    public function __construct()
    {
        parent::__construct();

        return $this->options([
            'maintainAspectRatio' => false,
            'fill' => true,
            'lineTension' => 0.3,
            'backgroundColor' => '#a8d0f0',
            'borderColor' => '#4f92c7',
            'borderCapStyle' => 'butt',
            'borderDash' => [],
            'borderDashOffset' => 0.0,
            'borderJoinStyle' => 'miter',
            'borderWidth' => 1,
            'pointBorderColor' => '#4f92c7',
            'pointBackgroundColor' => '#fff',
            'pointBorderWidth' => 1,
            'pointHoverRadius' => 5,
            'pointHoverBorderColor' => 'rgba(220,220,220,1)',
            'pointHoverBorderWidth' => 2,
            'pointRadius' => 1,
            'pointHitRadius' => 10,
            'spanGaps' => false,
        ]);
    }
}
