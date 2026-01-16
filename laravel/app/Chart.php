<?php

namespace App;

class Chart
{
    public string $name;
    public int $width;
    public int $height;

    public array $labels = [];
    public int $nbDatasets = 0;
    public array $data = [];

    public array $chartData = [];

    public function __construct(string $name, int $width = 400, int $height = 400)
    {
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
    }

    public function setLabelsAndDatasets(array $datas): void
    {
        $this->labels = array_keys($datas);
        $this->nbDatasets = count(reset($datas)); // nombre de colonnes
        $this->data = array_values($datas);
    }

    public function bar(array $labels, array $backgroundColors): void
    {
        $datasets = [];
        for ($i = 0; $i < $this->nbDatasets; $i++) {
            $datasets[] = [
                'label' => $labels[$i],
                'backgroundColor' => $backgroundColors[$i],
                'data' => array_column($this->data, $i),
            ];
        }

        $this->chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $this->labels,
                'datasets' => $datasets,
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true],
                ],
            ],
        ];
    }

    public function line(array $labels, array $borderColors, array $backgroundColors = []): void
    {
        $datasets = [];
        for ($i = 0; $i < $this->nbDatasets; $i++) {
            $datasets[] = [
                'label' => $labels[$i],
                'borderColor' => $borderColors[$i],
                'backgroundColor' => $backgroundColors[$i] ?? $borderColors[$i],
                'fill' => false,
                'data' => array_column($this->data, $i),
            ];
        }

        $this->chartData = [
            'type' => 'line',
            'data' => [
                'labels' => $this->labels,
                'datasets' => $datasets,
            ],
            'options' => [
                'responsive' => true,
                'plugins' => ['legend' => ['display' => false]],
                'scales' => [
                    'y' => ['beginAtZero' => true],
                ],
            ],
        ];
    }

    public function pie(array $labels, array $backgroundColors, array $hoverColors, array $data): void
    {
        $datasets = [[
            'data' => $data,
            'backgroundColor' => $backgroundColors,
            'hoverBackgroundColor' => $hoverColors,
        ]];

        $this->chartData = [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
            'options' => [
                'plugins' => ['legend' => ['display' => false]],
            ],
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->chartData);
    }
}
