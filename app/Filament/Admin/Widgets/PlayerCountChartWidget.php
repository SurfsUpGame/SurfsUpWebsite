<?php

namespace App\Filament\Admin\Widgets;

use App\Services\SteamPlayerCountService;
use Filament\Widgets\ChartWidget;

class PlayerCountChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Player Count Over Time';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    // Refresh every 5 minutes
    protected static ?string $pollingInterval = '300s';

    protected function getData(): array
    {
        $steamService = new SteamPlayerCountService();
        $chartData = $steamService->getChartData();

        $playerCounts = array_column($chartData, 'count');
        $labels = array_column($chartData, 'label');

        // If labels are not set, create them from timestamps
        if (empty($labels)) {
            $labels = array_map(function($entry) {
                return \Carbon\Carbon::parse($entry['timestamp'])->setTimezone('America/New_York')->format('M/d H:00');
            }, $chartData);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Players Online',
                    'data' => $playerCounts,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#10b981',
                    'pointBorderColor' => '#047857',
                    'pointHoverBackgroundColor' => '#047857',
                    'pointHoverBorderColor' => '#065f46',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Players',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Time (72 Hour EST)',
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'elements' => [
                'point' => [
                    'radius' => 3,
                    'hoverRadius' => 6,
                ],
            ],
        ];
    }
}
