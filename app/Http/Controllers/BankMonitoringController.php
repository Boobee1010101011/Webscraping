<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankMonitoringResource;
use App\Models\BankMonitoring;
use Illuminate\Http\Request;

class BankMonitoringController extends MonitoringController
{
    public function index(Request $request)
    {
        $query = BankMonitoring::query();
        foreach (['bank_name', 'bank_type'] as $filter) {
            if ($request->filled($filter)) {
                $query->whereRaw('LOWER(TRIM('.$filter.')) = ?', [strtolower(trim((string) $request->input($filter)))]);
            }
        }

        return BankMonitoringResource::collection($this->indexQuery($query, $request));
    }

    public function show(string $id)
    {
        return new BankMonitoringResource(BankMonitoring::findOrFail($id));
    }

    public function renderReportView(Request $request)
    {
        $query = BankMonitoring::query();
        $this->applyDateFilters($query, $request);

        foreach (['bank_name', 'bank_type'] as $filter) {
            if ($request->filled($filter)) {
                $query->whereRaw('LOWER(TRIM('.$filter.')) = ?', [strtolower(trim((string) $request->input($filter)))]);
            }
        }

        $totalReports = (clone $query)->count();
        $recentReports = (clone $query)->where('timestamp', '>=', now()->subDays(7))->count();
        $bankCount = (clone $query)->whereNotNull('bank_name')->distinct()->count('bank_name');
        $bankNames = BankMonitoring::query()->whereNotNull('bank_name')->distinct()->orderBy('bank_name')->pluck('bank_name');
        $bankTypes = BankMonitoring::query()->whereNotNull('bank_type')->distinct()->orderBy('bank_type')->pluck('bank_type');
        $records = $query->orderByDesc('timestamp')->paginate(15)->withQueryString();

        return view('bank-monitoring', compact('records', 'totalReports', 'recentReports', 'bankCount', 'bankNames', 'bankTypes'));
    }
}
