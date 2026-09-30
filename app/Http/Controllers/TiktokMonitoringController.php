<?php

namespace App\Http\Controllers;

use App\Http\Resources\TiktokMonitoringResource;
use App\Models\TiktokMonitoring;
use Illuminate\Http\Request;

class TiktokMonitoringController extends MonitoringController
{
    public function index(Request $request)
    {
        $query = TiktokMonitoring::query();
        if ($request->filled('post_type')) {
            $query->whereRaw('LOWER(TRIM(post_type)) = ?', [strtolower(trim((string) $request->input('post_type')))]);
        }

        return TiktokMonitoringResource::collection($this->indexQuery($query, $request));
    }

    public function show(string $id)
    {
        return new TiktokMonitoringResource(TiktokMonitoring::findOrFail($id));
    }

    public function renderReportView(Request $request)
    {
        $query = TiktokMonitoring::query();
        $this->applyDateFilters($query, $request);

        foreach (['threat_level', 'country', 'post_type'] as $filter) {
            if ($request->filled($filter)) {
                $query->whereRaw('LOWER(TRIM('.$filter.')) = ?', [strtolower(trim((string) $request->input($filter)))]);
            }
        }

        if ($request->filled('competitor')) {
            $query->where('competitor_name', 'LIKE', '%'.trim($request->input('competitor')).'%');
        }

        $totalReports = (clone $query)->count();
        $recentReports = (clone $query)->where('timestamp', '>=', now()->subDays(7))->count();
        $competitorCount = (clone $query)->whereNotNull('competitor_name')->distinct()->count('competitor_name');
        $threatCounts = (clone $query)->selectRaw('LOWER(TRIM(threat_level)) as level, COUNT(*) as total')
            ->groupByRaw('LOWER(TRIM(threat_level))')->pluck('total', 'level');
        $scrapes = $query->orderByDesc('timestamp')->paginate(15)->withQueryString();
        $platform = 'tiktok';
        $filterAction = route('tiktok-report');

        return view('scrape-report', compact('scrapes', 'totalReports', 'recentReports', 'competitorCount', 'threatCounts', 'platform', 'filterAction'));
    }
}
