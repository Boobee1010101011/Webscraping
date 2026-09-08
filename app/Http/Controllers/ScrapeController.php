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

        if ($request->has('threat_level')) {
            $query->where('threat_level', $request->threat_level);
        }

        if ($request->has('competitor')) {
            $query->where('competitor_name', 'LIKE', '%' . $request->competitor . '%');
        }

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
        $scrapes = Scrape::query()
            ->when($request->threat_level, function ($query, $level) {
                $query->where('threat_level', $level);
            })
            ->when($request->competitor, function ($query, $competitor) {
                $query->where('competitor_name', 'LIKE', '%' . $competitor . '%');
            })
            ->orderBy('timestamp', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('scrape-report', compact('scrapes'));
    }
}
