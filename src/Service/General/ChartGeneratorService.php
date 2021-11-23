<?php

namespace App\Service\General;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartGeneratorService {

    private $chartBuilder;

    public function __construct( ChartBuilderInterface $chartBuilder )
    {
        $this->chartBuilder = $chartBuilder;
    }

    public function monthInitialize(){
        return  $dataMonth = [
                    "Jan" => 0,
                    "Feb" => 0,
                    "Mar" => 0,
                    "Apr" => 0,
                    "May" => 0,
                    "Jun" => 0,
                    "Jul" => 0,
                    "Aug" => 0,
                    "Sep" => 0,
                    "Oct" => 0,
                    "Sep" => 0,
                    "Oct" => 0,
                    "Nov" => 0,
                    "Dec" => 0
                ];
    }

    public function generateChart($dataMonth, $entityName){

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet' , 'Aout' , 'Septembre' , 'Octobre' , 'Novembre' , 'Decembre'],
            'datasets' => [
                [
                    'label' => $entityName,
                    'backgroundColor' => '#d87444',
                    'borderColor' => '#074666;',
                    'data' => [ $dataMonth["Jan"] , $dataMonth["Feb"] , $dataMonth["Mar"] , $dataMonth["Apr"] , $dataMonth["May"] , $dataMonth["Jun"] , $dataMonth["Jul"] , $dataMonth["Aug"] , $dataMonth["Sep"] , $dataMonth["Oct"] , $dataMonth["Nov"] , $dataMonth["Dec"] ],
                ],
            ],
        ]);


        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' => ['min' => 0, 'max' => 10]],
                ],
            ],
        ]);

        return $chart;
    }
}

