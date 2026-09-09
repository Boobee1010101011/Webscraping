<?php

namespace App\Http\Controllers;

use App\Models\Scrape;
use App\Http\Resources\ScrapeResource;
use Illuminate\Http\Request;

class ScrapeController extends Controller
{
    public function index(Request $request)
    {
        $query = Scrape::query();

        $this->applyFilters($query, $request);

        $scrapes = $query->orderBy('timestamp', 'desc')->paginate($request->get('per_page', 15));

        return ScrapeResource::collection($scrapes);
    }

    public function show($id)
    {
        $scrape = Scrape::findOrFail($id);

        return new ScrapeResource($scrape);
    }

    public function renderReportView(Request $request)
    {
        $query = Scrape::query();
        $this->applyFilters($query, $request);

        $totalReports = (clone $query)->count();
        $recentReports = (clone $query)->where('timestamp', '>=', now()->subDays(7))->count();
        $competitorCount = (clone $query)
            ->whereNotNull('competitor_name')
            ->distinct()
            ->count('competitor_name');
        $threatCounts = (clone $query)
            ->selectRaw('LOWER(TRIM(threat_level)) as level, COUNT(*) as total')
            ->groupByRaw('LOWER(TRIM(threat_level))')
            ->pluck('total', 'level');

        $scrapes = $query->orderBy('timestamp', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('scrape-report', compact(
            'scrapes',
            'totalReports',
            'recentReports',
            'competitorCount',
            'threatCounts'
        ));
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('threat_level')) {
            $level = strtolower(trim((string) $request->input('threat_level')));

            $query->whereRaw('LOWER(TRIM(threat_level)) = ?', [$level]);
        }

        if ($request->filled('competitor')) {
            $query->where('competitor_name', 'LIKE', '%' . trim($request->input('competitor')) . '%');
        }
    }
}
