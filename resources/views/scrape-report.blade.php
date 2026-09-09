<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Competitor Intelligence</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { font-family: Inter, ui-sans-serif, system-ui, sans-serif; color-scheme: light; }
        html.dark { color-scheme: dark; }
        * { box-sizing: border-box; }
        body { margin: 0; min-width: 320px; background: #f1f5f9; color: #0f172a; }
        .shell { min-height: 100vh; padding: 28px 20px 56px; }
        .container { width: min(1440px, 100%); margin: auto; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
        .brand { display: flex; align-items: center; gap: 13px; }
        .brand-mark { display: grid; width: 46px; height: 46px; place-items: center; border-radius: 15px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; font-size: 22px; font-weight: 800; box-shadow: 0 10px 24px rgba(79, 70, 229, .25); }
        .eyebrow { margin: 0 0 4px; color: #6366f1; font-size: 11px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        h1 { margin: 0; color: #0f172a; font-size: clamp(25px, 3vw, 36px); font-weight: 850; letter-spacing: -.045em; }
        .subtitle { margin: 5px 0 0; color: #64748b; font-size: 14px; }
        .theme-button { border: 1px solid #cbd5e1; border-radius: 10px; background: white; color: #334155; cursor: pointer; font-size: 13px; font-weight: 700; padding: 10px 14px; }
        .theme-button:hover { background: #f8fafc; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 14px; }
        .stat { min-height: 140px; border: 1px solid; border-radius: 17px; padding: 20px; box-shadow: 0 8px 24px rgba(15, 23, 42, .05); }
        .stat-label { margin: 0; font-size: 13px; font-weight: 700; }
        .stat-value { margin: 15px 0 3px; font-size: 32px; font-weight: 850; letter-spacing: -.04em; }
        .stat-note { margin: 0; font-size: 11px; opacity: .75; }
        .stat-indigo { border-color: #c7d2fe; background: #eef2ff; color: #312e81; }
        .stat-rose { border-color: #fecdd3; background: #fff1f2; color: #881337; }
        .stat-emerald { border-color: #a7f3d0; background: #ecfdf5; color: #065f46; }
        .stat-amber { border-color: #fde68a; background: #fffbeb; color: #78350f; }
        .dashboard-grid { display: grid; grid-template-columns: 1.25fr .75fr; gap: 14px; margin-bottom: 14px; }
        .panel { min-width: 0; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 18px; background: white; box-shadow: 0 8px 24px rgba(15, 23, 42, .05); }
        .panel-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 15px; padding: 22px 22px 0; }
        .panel-title { margin: 0; color: #0f172a; font-size: 16px; font-weight: 800; }
        .panel-note { margin: 5px 0 0; color: #64748b; font-size: 13px; }
        .pill { display: inline-flex; align-items: center; white-space: nowrap; border-radius: 999px; background: #eef2ff; color: #4338ca; font-size: 11px; font-weight: 800; padding: 6px 10px; }
        .chart-body { display: flex; align-items: center; gap: clamp(25px, 5vw, 75px); padding: 26px 22px 30px; }
        .donut { position: relative; width: 170px; height: 170px; flex: 0 0 auto; border-radius: 50%; }
        .donut-hole { position: absolute; inset: 25px; display: grid; place-items: center; border-radius: 50%; background: white; text-align: center; }
        .donut-total { display: block; color: #0f172a; font-size: 27px; font-weight: 850; line-height: 1; }
        .donut-label { display: block; margin-top: 5px; color: #64748b; font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .legend { display: grid; flex: 1; gap: 14px; min-width: 150px; }
        .legend-row, .signal-row { display: flex; align-items: center; justify-content: space-between; gap: 15px; font-size: 13px; }
        .legend-name { display: flex; align-items: center; gap: 8px; color: #475569; }
        .legend-dot { width: 9px; height: 9px; border-radius: 50%; }
        .legend-value, .signal-value { color: #0f172a; font-weight: 800; }
        .signals { display: grid; gap: 0; padding: 15px 22px 22px; }
        .signal-row { border-bottom: 1px solid #e2e8f0; padding: 15px 0; }
        .signal-row:last-child { border-bottom: 0; }
        .signal-label { color: #64748b; }
        .reports { margin-top: 14px; }
        .filters { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; background: #f8fafc; padding: 17px 22px; }
        .filters select, .filters input { min-height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; background: white; color: #0f172a; font: inherit; font-size: 13px; padding: 0 12px; }
        .filters input { min-width: 230px; flex: 1; }
        .filter-button { min-height: 42px; border: 0; border-radius: 10px; background: #4f46e5; color: white; cursor: pointer; font-size: 13px; font-weight: 800; padding: 0 18px; }
        .filter-button:hover { background: #4338ca; }
        .clear-link { color: #64748b; font-size: 13px; font-weight: 600; text-decoration: none; }
        .clear-link:hover { color: #4f46e5; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; min-width: 900px; border-collapse: collapse; }
        th { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 800; letter-spacing: .1em; padding: 14px 18px; text-align: left; text-transform: uppercase; }
        td { border-top: 1px solid #e2e8f0; color: #475569; font-size: 13px; padding: 17px 18px; vertical-align: top; }
        td:first-child { color: #0f172a; font-weight: 800; }
        tr:hover td { background: #f8fafc; }
        .summary { display: -webkit-box; max-width: 280px; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .status-pill { display: inline-flex; border-radius: 999px; font-size: 11px; font-weight: 800; padding: 5px 9px; }
        .pagination { padding: 18px 22px; }
        html.dark body { background: #020617; color: #f8fafc; }
        html.dark h1, html.dark .panel-title, html.dark .donut-total, html.dark .legend-value, html.dark .signal-value, html.dark td:first-child { color: #f8fafc; }
        html.dark .subtitle, html.dark .panel-note, html.dark .legend-name, html.dark .signal-label { color: #94a3b8; }
        html.dark .theme-button, html.dark .panel { border-color: #1e293b; background: #0f172a; color: #e2e8f0; }
        html.dark .theme-button:hover, html.dark .filters, html.dark th { background: #1e293b; }
        html.dark .filters, html.dark td, html.dark .signal-row { border-color: #1e293b; }
        html.dark .filters select, html.dark .filters input { border-color: #334155; background: #1e293b; color: #f8fafc; }
        html.dark td { color: #cbd5e1; }
        html.dark tr:hover td { background: #172033; }
        html.dark .donut-hole { background: #0f172a; }
        html.dark .pill { background: #1e1b4b; color: #a5b4fc; }
        @media (max-width: 900px) { .stats { grid-template-columns: repeat(2, 1fr); } .dashboard-grid { grid-template-columns: 1fr; } }
        @media (max-width: 560px) { .shell { padding: 20px 12px 36px; } .topbar { align-items: flex-start; } .stats { grid-template-columns: 1fr; } .chart-body { align-items: flex-start; flex-direction: column; } .filters input { min-width: 100%; } }
    </style>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark');
    </script>
</head>
<body>
@php
    $selectedThreat = strtolower(trim((string) request('threat_level')));
    $highCount = (int) $threatCounts->get('high', 0);
    $mediumCount = (int) $threatCounts->get('medium', 0);
    $lowCount = (int) $threatCounts->get('low', 0);
    $noneCount = (int) $threatCounts->get('none', 0);
    $otherCount = max(0, $totalReports - $highCount - $mediumCount - $lowCount - $noneCount);
    $chartTotal = max(1, $totalReports);
    $highEnd = $highCount / $chartTotal * 100;
    $mediumEnd = $highEnd + ($mediumCount / $chartTotal * 100);
    $lowEnd = $mediumEnd + ($lowCount / $chartTotal * 100);
    $noneEnd = $lowEnd + ($noneCount / $chartTotal * 100);
    $topThreat = $threatCounts->sortDesc()->keys()->first();
@endphp
<div class="shell"><div class="container">
    <header class="topbar">
        <div class="brand"><div class="brand-mark">+</div><div><p class="eyebrow">Monitoring dashboard</p><h1>Competitor Intelligence</h1><p class="subtitle">Track market signals and respond before they become threats.</p></div></div>
        <button type="button" class="theme-button" onclick="toggleTheme()"><span id="theme-label">Dark mode</span></button>
    </header>

    <section class="stats">
        <div class="stat stat-indigo"><p class="stat-label">Total reports</p><p class="stat-value">{{ number_format($totalReports) }}</p><p class="stat-note">Matching current filters</p></div>
        <div class="stat stat-rose"><p class="stat-label">High threat</p><p class="stat-value">{{ number_format($highCount) }}</p><p class="stat-note">Needs attention first</p></div>
        <div class="stat stat-emerald"><p class="stat-label">Competitors</p><p class="stat-value">{{ number_format($competitorCount) }}</p><p class="stat-note">Unique monitored names</p></div>
        <div class="stat stat-amber"><p class="stat-label">Last 7 days</p><p class="stat-value">{{ number_format($recentReports) }}</p><p class="stat-note">Recent intelligence reports</p></div>
    </section>

    <section class="dashboard-grid">
        <div class="panel"><div class="panel-head"><div><h2 class="panel-title">Threat distribution</h2><p class="panel-note">Breakdown of the currently visible reports</p></div><span class="pill">Top: {{ $topThreat ?: 'None' }}</span></div><div class="chart-body">
            <div class="donut" style="background: conic-gradient(#f43f5e 0 {{ $highEnd }}%, #f59e0b {{ $highEnd }}% {{ $mediumEnd }}%, #10b981 {{ $mediumEnd }}% {{ $lowEnd }}%, #6366f1 {{ $lowEnd }}% {{ $noneEnd }}%, #64748b {{ $noneEnd }}% 100%);"><div class="donut-hole"><div><span class="donut-total">{{ number_format($totalReports) }}</span><span class="donut-label">Reports</span></div></div></div>
            <div class="legend">@foreach ([['High', $highCount, '#f43f5e'], ['Medium', $mediumCount, '#f59e0b'], ['Low', $lowCount, '#10b981'], ['None', $noneCount + $otherCount, '#6366f1']] as [$label, $count, $color])<div class="legend-row"><span class="legend-name"><span class="legend-dot" style="background:{{ $color }}"></span>{{ $label }}</span><span class="legend-value">{{ number_format($count) }}</span></div>@endforeach</div>
        </div></div>
        <div class="panel"><div class="panel-head"><div><h2 class="panel-title">Dashboard signals</h2><p class="panel-note">Quick context for the current view</p></div></div><div class="signals">
            <div class="signal-row"><span class="signal-label">High-threat share</span><span class="signal-value" style="color:#e11d48">{{ $totalReports ? number_format($highCount / $totalReports * 100, 1) : 0 }}%</span></div>
            <div class="signal-row"><span class="signal-label">Most common level</span><span class="signal-value" style="text-transform:capitalize">{{ $topThreat ?: 'None' }}</span></div>
            <div class="signal-row"><span class="signal-label">Data freshness</span><span class="signal-value" style="color:#059669">Live</span></div>
        </div></div>
    </section>

    <section class="panel reports"><div class="panel-head"><div><h2 class="panel-title">Intelligence reports</h2><p class="panel-note">Search, filter, and review competitor activity.</p></div><span class="pill">{{ number_format($totalReports) }} results</span></div>
        <form method="GET" action="{{ route('scrape-report') }}" class="filters"><select name="threat_level" onchange="this.form.submit()"><option value="">All threat levels</option><option value="High" {{ $selectedThreat === 'high' ? 'selected' : '' }}>High</option><option value="Medium" {{ $selectedThreat === 'medium' ? 'selected' : '' }}>Medium</option><option value="Low" {{ $selectedThreat === 'low' ? 'selected' : '' }}>Low</option><option value="None" {{ $selectedThreat === 'none' ? 'selected' : '' }}>None</option></select><input type="text" name="competitor" placeholder="Search competitor..." value="{{ request('competitor') }}"><button class="filter-button" type="submit">Apply filters</button>@if (request('threat_level') || request('competitor'))<a class="clear-link" href="{{ route('scrape-report') }}">Clear filters</a>@endif</form>
        <div class="table-wrap"><table><thead><tr><th>Competitor</th><th>Threat</th><th>Summary</th><th>AI Counter Strategy</th><th>Posted at</th><th>Action</th></tr></thead><tbody>
        @forelse ($scrapes as $scrape)
            @php($level = strtolower(trim((string) $scrape->threat_level)))
            <tr><td>{{ $scrape->competitor_name }}</td><td><span class="status-pill" style="background:{{ $level === 'high' ? '#ffe4e6' : ($level === 'medium' ? '#fef3c7' : '#d1fae5') }};color:{{ $level === 'high' ? '#be123c' : ($level === 'medium' ? '#b45309' : '#047857') }}">{{ $scrape->threat_level ?: 'None' }}</span></td><td><span class="summary" title="{{ $scrape->english_summary }}">{{ $scrape->english_summary ?: 'No summary available.' }}</span></td><td><span class="summary" title="{{ $scrape->ai_counter_strategy_draft }}">{{ $scrape->ai_counter_strategy_draft ?: 'No strategy available.' }}</span></td><td style="white-space:nowrap">{{ $scrape->timestamp ? $scrape->timestamp->timezone('Asia/Phnom_Penh')->format('M d, Y - h:i A') : 'N/A' }}</td><td style="white-space:nowrap">@if ($scrape->source_url)<a href="{{ $scrape->source_url }}" target="_blank" rel="noopener" style="color:#4f46e5;font-weight:700;text-decoration:none">View post</a>@else N/A @endif</td></tr>
        @empty
            <tr><td colspan="6" style="padding:45px;text-align:center;color:#64748b">No intelligence reports found.</td></tr>
        @endforelse
        </tbody></table></div><div class="pagination">{{ $scrapes->links() }}</div>
    </section>
</div></div>
<script>
    function updateThemeLabel() { document.getElementById('theme-label').textContent = document.documentElement.classList.contains('dark') ? 'Light mode' : 'Dark mode'; }
    function toggleTheme() { document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'; updateThemeLabel(); }
    updateThemeLabel();
</script>
</body>
</html>
