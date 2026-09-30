<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class MonitoringController extends Controller
{
    protected function indexQuery(Builder $query, Request $request)
    {
        $this->applyDateFilters($query, $request);

        if ($request->filled('threat_level')) {
            $query->whereRaw('LOWER(TRIM(threat_level)) = ?', [strtolower(trim((string) $request->input('threat_level')))]);
        }

        if ($request->filled('competitor')) {
            $query->where('competitor_name', 'LIKE', '%'.trim($request->input('competitor')).'%');
        }

        if ($request->filled('country')) {
            $query->whereRaw('LOWER(TRIM(country)) = ?', [strtolower(trim((string) $request->input('country')))]);
        }

        return $query->orderByDesc('timestamp')->paginate($request->integer('per_page', 15));
    }

    protected function applyDateFilters(Builder $query, Request $request): void
    {
        if ($request->filled('date')) {
            $date = Carbon::createFromFormat('!Y-m-d', (string) $request->input('date'));

            if ($date && $date->format('Y-m-d') === $request->input('date')) {
                $query->whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()]);
            }

            return;
        }

        match ($request->input('date_range')) {
            '1h' => $query->where('created_at', '>=', now()->subHour()),
            '1d' => $query->where('created_at', '>=', now()->subDay()),
            '1w' => $query->where('created_at', '>=', now()->subWeek()),
            default => null,
        };
    }
}
