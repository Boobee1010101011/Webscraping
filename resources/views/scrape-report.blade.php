@php
    $highCount = (int) $threatCounts->get('high', 0);
    $mediumCount = (int) $threatCounts->get('medium', 0);
    $lowCount = (int) $threatCounts->get('low', 0);
    $noneCount = (int) $threatCounts->get('none', 0);
    $otherCount = max(0, $totalReports - $highCount - $mediumCount - $lowCount - $noneCount);
    $chartTotal = max(1, $totalReports);
    $highEnd = ($highCount / $chartTotal) * 100;
    $mediumEnd = $highEnd + ($mediumCount / $chartTotal) * 100;
    $lowEnd = $mediumEnd + ($lowCount / $chartTotal) * 100;
    $topThreat = $threatCounts->sortDesc()->keys()->first();
    $selectedThreat = strtolower(trim((string) request('threat_level')));
    $selectedCountry = strtolower(trim((string) request('country')));
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPI Competitor Monitoring</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            color-scheme: light;
            --ink: #171a18;
            --muted: #737a76;
            --line: #e4e6df;
            --paper: #fbfaf6;
            --lime: #4da398;
            --accent-ink: #102321;
            --coral: #ff6b61;
            --amber: #f6bd4b;
            --mint: #86ddb0
        }

        html.dark {
            color-scheme: dark;
            --ink: #f2f5ee;
            --muted: #a2aca4;
            --line: #343c36;
            --paper: #151916;
        }

        * {
            box-sizing: border-box
        }

        html,
        body {
            margin: 0;
            min-width: 320px;
            background: var(--paper);
            color: var(--ink)
        }

        body {
            font-size: 14px
        }

        button,
        input,
        select {
            font: inherit
        }

        button,
        select {
            cursor: pointer
        }

        .app {
            min-height: 100vh
        }

        .topbar {
            height: 66px;
            display: flex;
            align-items: center;
            gap: 36px;
            padding: 0 42px;
            border-bottom: 1px solid var(--line);
            background: rgba(251, 250, 246, .94)
        }

        html.dark .topbar {
            background: rgba(21, 25, 22, .94)
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-right: 22px;
            white-space: nowrap
        }

        .brand-mark {
            width: 24px;
            height: 24px;
            display: grid;
            place-items: center;
            background: var(--lime);
            color: var(--accent-ink);
            font-size: 17px;
            font-weight: 900;
            line-height: 1
        }

        .brand-name {
            font-size: 15px;
            font-weight: 850;
            letter-spacing: -.035em
        }

        .nav {
            display: flex;
            align-self: stretch;
            gap: 28px
        }

        .nav a {
            position: relative;
            display: flex;
            align-items: center;
            color: #777c78;
            font-size: 13px;
            font-weight: 650;
            text-decoration: none
        }

        .nav a.active {
            color: var(--ink)
        }

        .nav a.active:after {
            position: absolute;
            right: 0;
            bottom: -1px;
            left: 0;
            height: 3px;
            background: var(--lime);
            content: ""
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-left: auto
        }

        .icon-button {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border: 0;
            background: transparent;
            color: #343a36
        }

        html.dark .icon-button {
            color: var(--ink)
        }

        .theme-toggle {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border: 1px solid var(--line);
            border-radius: 50%;
            background: transparent;
            color: var(--ink)
        }

        .theme-toggle svg {
            width: 17px;
            height: 17px
        }

        .icon-button svg {
            width: 18px;
            height: 18px
        }

        .scan-button,
        .primary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 0;
            background: var(--lime);
            color: var(--accent-ink);
            font-size: 13px;
            font-weight: 850
        }

        .scan-button {
            height: 40px;
            padding: 0 17px
        }

        .scan-button svg {
            width: 16px;
            height: 16px
        }

        .avatar {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #ecece7;
            color: #343936;
            font-size: 12px;
            font-weight: 800
        }

        html.dark .avatar {
            background: #2b332d;
            color: var(--ink)
        }

        html.dark .analytics,
        html.dark .reports {
            background: #1c211d
        }

        html.dark .donut:after {
            background: #1c211d
        }

        html.dark .filters select,
        html.dark .filters input {
            border-color: #465148;
            background: #202720;
            color: var(--ink)
        }

        html.dark th {
            background: #252c26;
            color: #a2aca4
        }

        html.dark tbody tr:hover {
            background: #293229
        }

        html.dark .nav a {
            color: #b8c3bd
        }

        html.dark .metric-icon,
        html.dark .metric-label,
        html.dark .section-link,
        html.dark .legend-row,
        html.dark td,
        html.dark .action-button,
        html.dark .detail-close {
            color: #c5d0ca
        }

        html.dark .metric-change,
        html.dark .live {
            color: #74d0a5
        }

        html.dark .signal-title {
            color: #edf4ef
        }

        html.dark .signal-time,
        html.dark th,
        html.dark .detail-section h3 {
            color: #aebbb3
        }

        html.dark .detail-text {
            color: #d6e0da
        }

        html.dark .detail-link {
            color: #74d0c1
        }

        html.dark .result-count {
            background: #2b3935;
            color: #c5d0ca
        }

        html.dark .pagination a,
        html.dark .pagination span {
            color: #c5d0ca
        }

        main {
            width: min(1440px, calc(100% - 84px));
            margin: 0 auto;
            padding: 38px 0 58px
        }

        .page-heading {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 24px;
            margin-bottom: 30px;
            text-align: center
        }

        h1,
        h2,
        p {
            margin: 0
        }

        h1 {
            font-size: clamp(32px, 4vw, 48px);
            letter-spacing: -.06em;
            line-height: 1;
            font-weight: 900
        }

        .updated {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 11px;
            color: var(--muted);
            font-size: 12px
        }

        .live {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #14834d;
            font-weight: 800
        }

        .live:before {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #26b768;
            content: ""
        }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            margin-bottom: 34px;
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line)
        }

        .metric {
            display: grid;
            grid-template-columns: 44px auto;
            gap: 14px;
            align-items: center;
            justify-content: center;
            min-height: 102px;
            padding: 18px 24px;
            border-right: 1px solid var(--line)
        }

        .metric:first-child {
            padding-left: 24px
        }

        .metric:last-child {
            border-right: 0;
            padding-right: 24px
        }

        .metric-icon {
            color: #39413c
        }

        .metric-icon svg {
            width: 29px;
            height: 29px;
            stroke-width: 1.55
        }

        .metric-label {
            color: #4e5550;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase
        }

        .metric-line {
            display: flex;
            align-items: baseline;
            gap: 13px;
            margin-top: 6px
        }

        .metric-value {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -.06em;
            line-height: 1
        }

        .metric-change {
            color: #147945;
            font-size: 12px;
            font-weight: 800
        }

        .metric-note {
            margin-top: 6px;
            color: var(--muted);
            font-size: 12px
        }

        .analytics {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 284px;
            border: 1px solid var(--line);
            background: #fffefa
        }

        .analytics-section {
            padding: 22px 24px
        }

        .analytics-section+.analytics-section {
            border-left: 1px solid var(--line)
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 23px
        }

        h2 {
            font-size: 18px;
            font-weight: 850;
            letter-spacing: -.045em
        }

        .section-link {
            display: flex;
            align-items: center;
            gap: 7px;
            border: 0;
            background: transparent;
            color: #505651;
            font-size: 12px
        }

        .section-link svg {
            width: 15px;
            height: 15px
        }

        .distribution {
            display: flex;
            align-items: center;
            gap: 42px
        }

        .donut {
            position: relative;
            width: 168px;
            height: 168px;
            flex: 0 0 auto;
            border-radius: 50%;
            background: conic-gradient(var(--coral) 0 {{ $highEnd }}%, var(--amber) {{ $highEnd }}% {{ $mediumEnd }}%, var(--mint) {{ $mediumEnd }}% {{ $lowEnd }}%, #d7e1d5 {{ $lowEnd }}% 100%)
        }

        .donut:after {
            position: absolute;
            inset: 23px;
            border-radius: 50%;
            background: #fffefa;
            content: ""
        }

        .donut-label {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: grid;
            place-content: center;
            text-align: center
        }

        .donut-label strong {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -.06em
        }

        .donut-label span {
            margin-top: 4px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase
        }

        .legend {
            display: grid;
            width: min(260px, 100%);
            gap: 17px
        }

        .legend-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            color: #3f4641;
            font-size: 13px
        }

        .legend-name {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%
        }

        .legend-value {
            color: var(--ink);
            font-weight: 800
        }

        .signals {
            display: grid;
            gap: 0
        }

        .signal-row {
            display: grid;
            grid-template-columns: 10px 1fr auto;
            gap: 11px;
            align-items: start;
            padding: 12px 0;
            border-bottom: 1px solid var(--line)
        }

        .signal-row:first-child {
            padding-top: 0
        }

        .signal-row:last-child {
            border-bottom: 0
        }

        .signal-dot {
            width: 10px;
            height: 10px;
            margin-top: 4px;
            border-radius: 50%
        }

        .signal-title {
            color: #252b27;
            font-size: 13px;
            font-weight: 800
        }

        .signal-summary {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px
        }

        .signal-time {
            color: #858b86;
            font-size: 11px;
            white-space: nowrap
        }

        .reports {
            margin-top: 22px;
            border: 1px solid var(--line);
            background: #fffefa
        }

        .reports-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 20px 20px 16px
        }

        .reports-title {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .result-count {
            padding: 5px 9px;
            border-radius: 99px;
            background: #f0f0eb;
            color: #5c635e;
            font-size: 11px;
            font-weight: 800
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            padding: 0 20px 17px
        }

        .filters select,
        .filters input {
            height: 39px;
            border: 1px solid #dfe2da;
            border-radius: 4px;
            outline: none;
            background: #fffefa;
            color: var(--ink);
            font-size: 12px
        }

        .filters select {
            min-width: 145px;
            padding: 0 30px 0 11px
        }

        .filters input {
            min-width: 210px;
            flex: 1;
            padding: 0 12px
        }

        .filters select:focus,
        .filters input:focus {
            border-color: #9fae66;
            box-shadow: 0 0 0 3px rgba(215, 243, 74, .24)
        }

        .filter-button {
            height: 39px;
            padding: 0 17px
        }

        .clear-link {
            display: inline-flex;
            align-items: center;
            padding: 0 5px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none
        }

        .table-wrap {
            overflow-x: auto;
            border-top: 1px solid var(--line)
        }

        table {
            width: 100%;
            min-width: 980px;
            border-collapse: collapse
        }

        th {
            padding: 12px 20px;
            background: #f6f6f1;
            color: #858b86;
            font-size: 10px;
            font-weight: 850;
            letter-spacing: .09em;
            text-align: left;
            text-transform: uppercase
        }

        td {
            padding: 15px 20px;
            border-top: 1px solid var(--line);
            color: #4d554f;
            font-size: 12px;
            line-height: 1.45;
            vertical-align: top
        }

        tbody tr {
            transition: background .16s ease
        }

        tbody tr:hover {
            background: #f5f8e9
        }

        td:first-child {
            color: var(--ink);
            font-size: 13px;
            font-weight: 850
        }

        .alerted-time {
            display: grid;
            gap: 3px;
            white-space: nowrap
        }

        .alerted-time strong {
            color: var(--ink);
            font-size: 12px;
            font-weight: 800
        }

        .alerted-time small {
            color: var(--muted);
            font-size: 11px
        }

        .summary {
            display: -webkit-box;
            max-width: 265px;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2
        }

        .status-pill {
            display: inline-flex;
            min-width: 56px;
            justify-content: center;
            padding: 5px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 850
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #333a35;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap
        }

        .action-button svg {
            width: 15px;
            height: 15px;
            transition: transform .16s ease
        }

        .action-button:hover svg {
            transform: translateX(3px)
        }

        .pagination {
            padding: 16px 20px;
            border-top: 1px solid var(--line)
        }

        .pagination nav {
            font-size: 12px
        }

        .detail-backdrop {
            position: fixed;
            inset: 0;
            z-index: 20;
            background: rgba(23, 26, 24, .28);
            backdrop-filter: blur(2px)
        }

        .detail-backdrop[hidden] {
            display: none
        }

        .detail-panel {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 21;
            display: flex;
            width: min(430px, 100%);
            flex-direction: column;
            overflow: auto;
            border-left: 1px solid var(--line);
            background: var(--paper);
            box-shadow: -18px 0 50px rgba(23, 26, 24, .12);
            animation: slide-in .22s ease-out
        }

        .detail-panel[hidden] {
            display: none
        }

        @keyframes slide-in {
            from {
                transform: translateX(30px);
                opacity: 0
            }

            to {
                transform: translateX(0);
                opacity: 1
            }
        }

        .detail-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            padding: 30px 28px 22px;
            border-bottom: 1px solid var(--line)
        }

        .detail-head h2 {
            max-width: 320px;
            font-size: 25px
        }

        .detail-subtitle {
            margin-top: 8px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 750;
            letter-spacing: .03em;
            text-transform: uppercase
        }

        .detail-close {
            border: 0;
            background: transparent;
            color: #555d57;
            font-size: 25px;
            line-height: 1
        }

        .detail-body {
            display: grid;
            gap: 18px;
            padding: 22px 28px 30px
        }

        .detail-section {
            padding-bottom: 18px;
            border-bottom: 1px solid var(--line)
        }

        .detail-section:last-child {
            border-bottom: 0
        }

        .detail-section h3 {
            margin: 0 0 9px;
            color: #858b86;
            font-size: 10px;
            font-weight: 850;
            letter-spacing: .1em;
            text-transform: uppercase
        }

        .detail-text {
            color: #343c36;
            font-size: 14px;
            line-height: 1.65;
            white-space: pre-wrap;
            overflow-wrap: anywhere
        }

        .detail-images {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 9px
        }

        .detail-images img {
            width: 100%;
            border: 1px solid var(--line);
            cursor: zoom-in
        }

        .detail-link {
            color: #4e6812;
            font-size: 12px;
            font-weight: 800;
            overflow-wrap: anywhere
        }

        .lightbox {
            position: fixed;
            inset: 0;
            z-index: 30;
            display: flex;
            flex-direction: column;
            background: rgba(10, 12, 11, .95)
        }

        .lightbox[hidden] {
            display: none
        }

        .lightbox-toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 7px;
            padding: 14px 18px
        }

        .lightbox-toolbar button {
            min-width: 37px;
            border: 1px solid #515950;
            border-radius: 4px;
            background: #202521;
            color: #fff;
            padding: 7px 10px
        }

        .lightbox-viewport {
            flex: 1;
            overflow: auto;
            padding: 0 28px 28px;
            text-align: center
        }

        .lightbox-image {
            display: inline-block;
            max-width: none;
            transform-origin: top center;
            vertical-align: top
        }

        @media(max-width:900px) {
            .topbar {
                padding: 0 22px;
                gap: 20px
            }

            .brand {
                margin-right: 0
            }

            .nav {
                gap: 16px
            }

            main {
                width: min(100% - 44px, 720px)
            }

            .metric-row {
                grid-template-columns: repeat(2, 1fr)
            }

            .metric:nth-child(2) {
                border-right: 0
            }

            .metric:nth-child(n+3) {
                border-top: 1px solid var(--line)
            }

            .metric:nth-child(3) {
                padding-left: 24px
            }

            .analytics {
                grid-template-columns: 1fr
            }

            .analytics-section+.analytics-section {
                border-top: 1px solid var(--line);
                border-left: 0
            }
        }

        @media(max-width:560px) {
            .topbar {
                height: auto;
                min-height: 66px;
                flex-wrap: wrap;
                padding: 14px 16px
            }

            .nav {
                order: 3;
                width: 100%;
                height: 36px
            }

            .top-actions {
                gap: 6px
            }

            .top-actions .icon-button,
            .avatar {
                display: none
            }

            main {
                width: calc(100% - 28px);
                padding-top: 28px
            }

            .page-heading {
                display: block
            }

            .updated {
                margin-top: 15px
            }

            .metric-row {
                grid-template-columns: 1fr 1fr
            }

            .metric {
                grid-template-columns: 1fr;
                gap: 7px;
                min-height: 110px;
                padding: 16px 10px;
                justify-items: center;
                text-align: center
            }

            .metric:first-child {
                padding-left: 10px
            }

            .metric-value {
                font-size: 26px
            }

            .metric-line {
                display: block
            }

            .metric-change {
                display: block;
                margin-top: 4px
            }

            .distribution {
                align-items: flex-start;
                flex-direction: column;
                gap: 25px
            }

            .donut {
                width: 142px;
                height: 142px
            }

            .donut:after {
                inset: 20px
            }

            .reports-head {
                align-items: flex-start;
                flex-direction: column
            }

            .filters select,
            .filters input,
            .filter-button {
                width: 100%
            }
        }

        @media(prefers-reduced-motion:reduce) {

            *,
            *:before,
            *:after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                transition-duration: .01ms !important
            }
        }
    </style>
</head>

<body>
    <div class="app">
        <header class="topbar">
            <div class="brand"><span class="brand-mark">↗</span><span class="brand-name">PULSE / INTEL</span></div>
            <nav class="nav" aria-label="Primary"><a class="{{ $platform === 'facebook' ? 'active' : '' }}"
                    href="{{ route('scrape-report') }}">Facebook</a><a class="{{ $platform === 'tiktok' ? 'active' : '' }}"
                    href="{{ route('tiktok-report') }}">TikTok</a><a href="{{ route('bank-monitoring') }}">Bank</a><a
                    href="{{ route('generated-content') }}">Generated content</a></nav>
            <div class="top-actions"><button class="icon-button" type="button" aria-label="Search"><svg
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="6.5" />
                        <path d="m16 16 5 5" />
                    </svg></button><button class="theme-toggle" type="button" aria-label="Switch to dark mode"><span
                        class="theme-icon"></span></button><button id="newScanBtn" class="scan-button" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    <span>New scan</span>
                </button><span class="avatar">PI</span></div>
        </header>
        <main>
            <div class="page-heading">
                <h1>Competitor Intelligence</h1>
                <div class="updated">Last updated
                    <span>{{ now()->timezone('Asia/Phnom_Penh')->format('M d, Y · h:i A') }}</span><span
                        class="live">Live</span>
                </div>
            </div>
            <section class="metric-row" aria-label="Report summary">
                @foreach ([['Total reports', $totalReports, '+12%', 'vs previous 7 days', 'file'], ['High threat', $highCount, '+8%', 'vs previous 7 days', 'alert'], ['Competitors', $competitorCount, '+0%', 'vs previous 7 days', 'users'], ['Last 7 days', $recentReports, '+23%', 'vs previous 7 days', 'calendar']] as [$label, $value, $change, $note, $icon])
                    <div class="metric"><span class="metric-icon"><svg viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path d="M6 3.5h8l4 4V20.5H6z" />
                                <path d="M14 3.5v4h4" />
                                @if ($icon === 'alert')
                                    <path d="m12 10 2.7 5h-5.4z" />
                                    <path d="M12 12v1.2" />
                                @elseif($icon === 'users')
                                    <circle cx="9" cy="9" r="3" />
                                    <path
                                        d="M3.5 19c.4-3 2.2-4.5 5.5-4.5s5.1 1.5 5.5 4.5M16 12a3 3 0 1 0 0-6M16 14.5c2.8 0 4.3 1.5 4.6 4.5" />
                                @elseif($icon === 'calendar')
                                    <rect x="4" y="5" width="16" height="15" rx="1" />
                                    <path d="M8 3v4M16 3v4M4 10h16" />
                                @endif
                            </svg>
                        </span>
                        <div>
                            <p class="metric-label">{{ $label }}</p>
                            <div class="metric-line"><strong
                                    class="metric-value">{{ number_format($value) }}</strong><span
                                    class="metric-change">△ {{ $change }}</span></div>
                            <p class="metric-note">{{ $note }}</p>
                        </div>
                    </div>
                @endforeach
            </section>
            <section class="analytics" id="signals">
                <div class="analytics-section">
                    <div class="section-head">
                        <h2>Threat distribution</h2>
                    </div>
                    <div class="distribution">
                        <div class="donut">
                            <div class="donut-label">
                                <strong>{{ number_format($totalReports) }}</strong><span>reports</span>
                            </div>
                        </div>
                        <div class="legend">
                            @foreach ([['High', $highCount, '#ff6b61'], ['Medium', $mediumCount, '#f6bd4b'], ['Low', $lowCount, '#86ddb0'], ['None', $noneCount + $otherCount, '#d7e1d5']] as [$label, $count, $color])
                                <div class="legend-row"><span class="legend-name"><i class="legend-dot"
                                            style="background:{{ $color }}"></i>{{ $label }}</span><span
                                        class="legend-value">{{ number_format($count) }} <small
                                            style="color:var(--muted);font-weight:600">({{ $totalReports ? number_format(($count / $totalReports) * 100) : 0 }}%)</small></span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="analytics-section">
                    <div class="section-head">
                        <h2>Dashboard signals</h2><button class="section-link" type="button">View all signals <svg
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M5 12h13M13 6l6 6-6 6" />
                            </svg></button>
                    </div>
                    <div class="signals">
                        <div class="signal-row"><i class="signal-dot" style="background:var(--coral)"></i>
                            <div><strong class="signal-title">High-threat share</strong>
                                <p class="signal-summary">
                                    {{ $totalReports ? number_format(($highCount / $totalReports) * 100, 1) : 0 }}% of
                                    visible
                                    reports need attention.</p>
                            </div><span class="signal-time">Now</span>
                        </div>
                        <div class="signal-row"><i class="signal-dot" style="background:var(--amber)"></i>
                            <div><strong class="signal-title">Most common level</strong>
                                <p class="signal-summary">{{ ucfirst($topThreat ?: 'None') }} is the leading signal in
                                    this view.</p>
                            </div><span class="signal-time">Today</span>
                        </div>
                        <div class="signal-row"><i class="signal-dot" style="background:var(--mint)"></i>
                            <div><strong class="signal-title">Data freshness</strong>
                                <p class="signal-summary">Live monitoring data is available.</p>
                            </div><span class="signal-time">Live</span>
                        </div>
                    </div>
                </div>
            </section>
            <section class="reports" id="reports">
                <div class="reports-head">
                    <div class="reports-title">
                        <h2>{{ $platform === 'tiktok' ? 'TikTok monitoring' : 'Facebook monitoring' }}</h2><span class="result-count">{{ number_format($totalReports) }}
                            results</span>
                    </div>
                </div>
                <form method="GET" action="{{ $filterAction }}" class="filters"><select
                        name="threat_level" onchange="this.form.submit()">
                        <option value="">All threat levels</option>
                        <option value="High" {{ $selectedThreat === 'high' ? 'selected' : '' }}>High</option>
                        <option value="Medium" {{ $selectedThreat === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="Low" {{ $selectedThreat === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="None" {{ $selectedThreat === 'none' ? 'selected' : '' }}>None</option>
                    </select><select name="country" onchange="this.form.submit()">
                        <option value="">All countries</option>
                        @foreach (['Cambodia', 'Vietnam', 'Malaysia', 'Singapore', 'Thailand'] as $country)
                            <option value="{{ $country }}"
                                {{ $selectedCountry === strtolower($country) ? 'selected' : '' }}>{{ $country }}
                            </option>
                        @endforeach
                    </select><select name="date_range" onchange="this.form.elements.date.value = ''; this.form.submit()">
                        <option value="">Any insertion time</option>
                        <option value="1h" {{ request('date_range') === '1h' ? 'selected' : '' }}>Inserted in the last hour</option>
                        <option value="1d" {{ request('date_range') === '1d' ? 'selected' : '' }}>Inserted in the last 24 hours</option>
                        <option value="1w" {{ request('date_range') === '1w' ? 'selected' : '' }}>Inserted in the last 7 days</option>
                    </select>
                    <input type="date" name="date" aria-label="Inserted on specific date" onchange="this.form.elements.date_range.value = ''; this.form.submit()" value="{{ request('date') }}">
                    @if ($platform === 'tiktok')
                        <select name="post_type" onchange="this.form.submit()">
                            <option value="">All post types</option>
                            @foreach (['Video', 'Live', 'Photo', 'Story'] as $postType)
                                <option value="{{ $postType }}" {{ strtolower((string) request('post_type')) === strtolower($postType) ? 'selected' : '' }}>{{ $postType }}</option>
                            @endforeach
                        </select>
                    @endif
                    <input type="text" name="competitor" placeholder="Search competitor..."
                        value="{{ request('competitor') }}"><button class="scan-button filter-button"
                        type="submit">Apply filters</button>
                    @if (request('threat_level') || request('country') || request('competitor') || request('post_type') || request('date_range') || request('date'))
                        <a class="clear-link" href="{{ $filterAction }}">Clear filters</a>
                    @endif
                </form>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Competitor</th>
                                <th>Threat</th>
                                <th>Summary</th>
                                <th>AI Counter Strategy</th>
                                <th>Posted at</th>
                                <th>Alerted at</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scrapes as $scrape)
                                @php($level = strtolower(trim((string) $scrape->threat_level)))
                                <tr>
                                    <td>{{ $scrape->competitor_name ?: 'Unknown competitor' }}</td>
                                    <td><span class="status-pill"
                                            style="background:{{ $level === 'high' ? '#ffe1de' : ($level === 'medium' ? '#fff0ca' : '#dff5e7') }};color:{{ $level === 'high' ? '#c73d35' : ($level === 'medium' ? '#976813' : '#277d51') }}">{{ $scrape->threat_level ?: 'None' }}</span>
                                    </td>
                                    <td><span class="summary"
                                            title="{{ $scrape->english_summary }}">{{ $scrape->english_summary ?: 'No summary available.' }}</span>
                                    </td>
                                    <td><span class="summary"
                                            title="{{ $scrape->ai_counter_strategy_draft }}">{{ $scrape->ai_counter_strategy_draft ?: 'No strategy available.' }}</span>
                                    </td>
                                    <td style="white-space:nowrap">
                                        {{ $scrape->timestamp ? $scrape->timestamp->timezone('Asia/Phnom_Penh')->format('M d, Y · h:i A') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if ($scrape->created_at)
                                            <span class="alerted-time" title="Inserted {{ $scrape->created_at->timezone('Asia/Phnom_Penh')->format('M d, Y h:i A') }}">
                                                <strong>{{ $scrape->created_at->timezone('Asia/Phnom_Penh')->format('M d, Y h:i A') }}</strong>
                                                <small>{{ $scrape->created_at->diffForHumans() }}</small>
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td><button type="button" class="action-button view-report"
                                            data-competitor="{{ $scrape->competitor_name }}"
                                            data-country="{{ $scrape->country }}"
                                            data-post-type="{{ $scrape->post_type }}"
                                            data-threat="{{ $scrape->threat_level }}"
                                            data-original-text="{{ $scrape->original_text ?? $scrape->caption ?? $scrape->transcript }}"
                                            data-summary="{{ $scrape->english_summary }}"
                                            data-strategy="{{ $scrape->ai_counter_strategy_draft }}"
                                            data-source-url="{{ $scrape->source_url }}"
                                            data-image-url="{{ $scrape->image_url }}">View details <svg
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8">
                                                <path d="M5 12h13M13 6l6 6-6 6" />
                                            </svg></button></td>
                            </tr>@empty<tr>
                                    <td colspan="7" style="padding:45px;text-align:center;color:var(--muted)">No
                                        intelligence reports found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagination">{{ $scrapes->links() }}</div>
            </section>
        </main>
    </div>
    <div id="detail-backdrop" class="detail-backdrop" hidden></div>
    <aside id="detail-panel" class="detail-panel" hidden role="dialog" aria-modal="true"
        aria-labelledby="detail-title">
        <div class="detail-head">
            <div>
                <h2 id="detail-title"></h2>
                <p id="detail-subtitle" class="detail-subtitle"></p>
            </div><button class="detail-close" type="button" aria-label="Close details">×</button>
        </div>
        <div class="detail-body">
            <section id="detail-images-section" class="detail-section" hidden>
                <h3>Images</h3>
                <div id="detail-images" class="detail-images"></div>
            </section>
            <section class="detail-section">
                <h3>Original text</h3>
                <p id="detail-original" class="detail-text"></p>
            </section>
            <section class="detail-section">
                <h3>AI counter strategy</h3>
                <p id="detail-strategy" class="detail-text"></p>
            </section>
            <section id="detail-link-section" class="detail-section" hidden>
                <h3>Source post</h3><a id="detail-link" class="detail-link" target="_blank" rel="noopener"></a>
            </section>
        </div>
    </aside>
    <div id="image-lightbox" class="lightbox" hidden role="dialog" aria-modal="true"
        aria-label="Full-size report image">
        <div class="lightbox-toolbar"><button id="zoom-out" type="button">−</button><button id="zoom-reset"
                type="button">100%</button><button id="zoom-in" type="button">+</button><button
                id="lightbox-close" type="button" aria-label="Close image viewer">×</button></div>
        <div class="lightbox-viewport"><img id="lightbox-image" class="lightbox-image" alt="Full-size report image">
        </div>
    </div>
    <script>
        const panel = document.getElementById('detail-panel'),
            backdrop = document.getElementById('detail-backdrop'),
            images = document.getElementById('detail-images'),
            imagesSection = document.getElementById('detail-images-section'),
            linkSection = document.getElementById('detail-link-section'),
            lightbox = document.getElementById('image-lightbox'),
            lightboxImage = document.getElementById('lightbox-image');
        let zoom = 1;

        function text(id, value) {
            document.getElementById(id).textContent = value || 'Not available.'
        }

        function urls(value) {
            if (!value) return [];
            try {
                const parsed = JSON.parse(value);
                return Array.isArray(parsed) ? parsed : [parsed]
            } catch (e) {
                return [value]
            }
        }

        function openDetails(button) {
            const d = button.dataset;
            text('detail-title', d.competitor || 'Intelligence report');
            text('detail-subtitle', [d.country, d.postType, d.threat].filter(Boolean).join(' · ') || 'Report details');
            text('detail-original', d.originalText);
            text('detail-strategy', d.strategy);
            images.replaceChildren();
            urls(d.imageUrl).filter(Boolean).forEach(url => {
                const image = document.createElement('img');
                image.src = url;
                image.alt = 'Report image (click to enlarge)';
                image.loading = 'lazy';
                image.onclick = () => openLightbox(url);
                images.appendChild(image)
            });
            imagesSection.hidden = !images.children.length;
            linkSection.hidden = !d.sourceUrl;
            if (d.sourceUrl) {
                const link = document.getElementById('detail-link');
                link.href = d.sourceUrl;
                link.textContent = d.sourceUrl
            }
            panel.hidden = false;
            backdrop.hidden = false;
            document.querySelector('.detail-close').focus()
        }

        function closeDetails() {
            panel.hidden = true;
            backdrop.hidden = true
        }

        function updateZoom() {
            lightboxImage.style.transform = `scale(${zoom})`;
            document.getElementById('zoom-reset').textContent = `${Math.round(zoom*100)}%`
        }

        function openLightbox(url) {
            lightboxImage.src = url;
            zoom = 1;
            updateZoom();
            lightbox.hidden = false
        }

        function closeLightbox() {
            lightbox.hidden = true;
            lightboxImage.removeAttribute('src')
        }

        function changeZoom(amount) {
            zoom = Math.min(4, Math.max(.25, zoom + amount));
            updateZoom()
        }
        document.addEventListener('click', event => {
            const button = event.target.closest('.view-report');
            if (button) openDetails(button);
        });
        document.querySelector('.detail-close').onclick = closeDetails;
        backdrop.onclick = closeDetails;
        document.getElementById('lightbox-close').onclick = closeLightbox;
        document.getElementById('zoom-in').onclick = () => changeZoom(.25);
        document.getElementById('zoom-out').onclick = () => changeZoom(-.25);
        document.getElementById('zoom-reset').onclick = () => {
            zoom = 1;
            updateZoom()
        };
        lightbox.addEventListener('click', e => {
            if (e.target === lightbox || e.target.classList.contains('lightbox-viewport')) closeLightbox()
        });
        lightbox.addEventListener('wheel', e => {
            e.preventDefault();
            changeZoom(e.deltaY < 0 ? .1 : -.1)
        }, {
            passive: false
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (!lightbox.hidden) closeLightbox();
                else if (!panel.hidden) closeDetails()
            }
        });

        document.querySelector('.scan-button').addEventListener('click', async function() {
            this.disabled = true;

            try {
                const response = await fetch('{{ route('scan.trigger') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    const payload = await response.json().catch(() => ({}));
                    throw new Error(payload.message || 'The scan could not be started.');
                }

                alert('Scan initiated!');
            } catch (error) {
                console.error('Error triggering scan:', error);
                alert(error.message || 'Unable to start the scan. Please try again.');
            } finally {
                this.disabled = false;
            }
        });
    </script>
</body>

</html>
