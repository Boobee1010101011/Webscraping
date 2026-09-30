<?php

namespace App\Http\Controllers;

use App\Http\Resources\FacebookMonitoringResource;
use App\Models\FacebookMonitoring;
use Illuminate\Http\Request;

class FacebookMonitoringController extends MonitoringController
{
    public function index(Request $request)
    {
        return FacebookMonitoringResource::collection($this->indexQuery(FacebookMonitoring::query(), $request));
    }

    public function show(string $id)
    {
        return new FacebookMonitoringResource(FacebookMonitoring::findOrFail($id));
    }

    public function renderReportView(Request $request)
    {
        $query = FacebookMonitoring::query();
        $this->applyDateFilters($query, $request);
        $this->applyFacebookFilters($query, $request);

        $totalReports = (clone $query)->count();
        $recentReports = (clone $query)->where('timestamp', '>=', now()->subDays(7))->count();
        $competitorCount = (clone $query)->whereNotNull('competitor_name')->distinct()->count('competitor_name');
        $threatCounts = (clone $query)->selectRaw('LOWER(TRIM(threat_level)) as level, COUNT(*) as total')
            ->groupByRaw('LOWER(TRIM(threat_level))')->pluck('total', 'level');
        $scrapes = $query->orderByDesc('timestamp')->paginate(15)->withQueryString();

        $platform = 'facebook';
        $filterAction = route('scrape-report');

        return view('scrape-report', compact('scrapes', 'totalReports', 'recentReports', 'competitorCount', 'threatCounts', 'platform', 'filterAction'));
    }

    private function applyFacebookFilters($query, Request $request): void
    {
        if ($request->filled('threat_level')) {
            $query->whereRaw('LOWER(TRIM(threat_level)) = ?', [strtolower(trim((string) $request->input('threat_level')))]);
        }
        if ($request->filled('competitor')) {
            $query->where('competitor_name', 'LIKE', '%'.trim($request->input('competitor')).'%');
        }
        if ($request->filled('country')) {
            $query->whereRaw('LOWER(TRIM(country)) = ?', [strtolower(trim((string) $request->input('country')))]);
        }
    }
}
