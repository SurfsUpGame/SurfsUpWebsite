<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PlayerCountHistory extends Model
{
    protected $table = 'player_count_history';
    
    protected $fillable = [
        'player_count',
        'app_id',
        'recorded_at',
    ];
    
    protected $casts = [
        'recorded_at' => 'datetime',
        'player_count' => 'integer',
    ];
    
    public static function recordPlayerCount(int $playerCount, string $appId = '3454830'): self
    {
        return self::create([
            'player_count' => $playerCount,
            'app_id' => $appId,
            'recorded_at' => now(),
        ]);
    }
    
    public static function getHistoryForLast24Hours(string $appId = '3454830'): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('app_id', $appId)
            ->where('recorded_at', '>=', now()->subHours(24))
            ->orderBy('recorded_at')
            ->get();
    }
    
    public static function getChartDataForLast24Hours(string $appId = '3454830'): array
    {
        $history = self::getHistoryForLast24Hours($appId);
        
        // Group by hour and average the counts
        $hourlyData = [];
        $now = now();
        
        for ($i = 23; $i >= 0; $i--) {
            $hour = $now->copy()->subHours($i);
            $hourStart = $hour->startOfHour();
            $hourEnd = $hour->copy()->endOfHour();
            
            $hourData = $history->filter(function ($entry) use ($hourStart, $hourEnd) {
                return $entry->recorded_at >= $hourStart && $entry->recorded_at <= $hourEnd;
            });
            
            $averageCount = $hourData->isEmpty() ? 0 : $hourData->avg('player_count');
            
            $hourlyData[] = [
                'timestamp' => $hour->toISOString(),
                'count' => round($averageCount),
                'label' => $hour->format('H:00'),
            ];
        }
        
        return $hourlyData;
    }
    
    public static function getPeakAndLowForLast24Hours(string $appId = '3454830'): array
    {
        $history = self::getHistoryForLast24Hours($appId);
        
        if ($history->isEmpty()) {
            return ['peak' => null, 'low' => null];
        }
        
        return [
            'peak' => $history->max('player_count'),
            'low' => $history->min('player_count'),
        ];
    }
    
    public static function cleanupOldRecords(int $daysToKeep = 30): int
    {
        return self::where('recorded_at', '<', now()->subDays($daysToKeep))->delete();
    }
}
