@extends('layouts.app')

@section('title', 'Tournament - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tournament.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ranking.css') }}">
    <style>
        .tournament-page { background: #f0f2f5; min-height: 100vh; padding: 30px 20px; }
        .main-tournament-schedule { display: grid; grid-template-columns: 220px 1fr 300px; gap: 24px; max-width: 1400px; margin: 0 auto; }
        @media (max-width: 1200px) { .main-tournament-schedule { grid-template-columns: 200px 1fr; } .right-sidebar { display: none; } }
        @media (max-width: 768px) { .main-tournament-schedule { grid-template-columns: 1fr; } .left-sidebar { display: none; } }

        /* Sidebar */
        .sidebar-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .sidebar-card h3 { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888; margin: 0 0 15px; padding-bottom: 10px; border-bottom: 1px solid #f0f0f0; }
        .sidebar-card ul { list-style: none; padding: 0; margin: 0; }
        .sidebar-card ul li a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #444; font-size: 0.9rem; font-weight: 500; transition: all 0.2s; }
        .sidebar-card ul li a:hover { background: #f5f0ff; color: #7c3aed; }
        .sidebar-card ul li a.active { background: #7c3aed; color: white; }
        .sidebar-card ul li a i { width: 18px; text-align: center; }

        /* Section card */
        .section-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 24px; }
        .section-card h2 { font-size: 1.1rem; font-weight: 700; color: #1a1a2e; margin: 0 0 20px; display: flex; align-items: center; gap: 10px; }
        .section-card h2 i { color: #7c3aed; }

        /* Filter */
        .filter-select { padding: 10px 16px; border-radius: 8px; border: 1px solid #e0e0e0; font-size: 0.9rem; background: #f8f9fa; color: #333; width: 100%; max-width: 280px; cursor: pointer; }
        .filter-select:focus { outline: none; border-color: #7c3aed; }

        /* Match day */
        .match-day { margin-bottom: 24px; }
        .match-day-header { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888; padding: 6px 12px; background: #f8f9fa; border-radius: 6px; margin-bottom: 12px; display: inline-block; }

        /* Match item */
        .match-item { display: flex; align-items: center; background: #fafafa; border: 1px solid #f0f0f0; border-radius: 10px; padding: 16px 20px; margin-bottom: 10px; transition: all 0.2s; }
        .match-item:hover { border-color: #7c3aed; box-shadow: 0 2px 12px rgba(124,58,237,0.08); background: white; }
        .match-item.live { border-left: 3px solid #ef4444; background: #fff5f5; }
        .match-item.finished { opacity: 0.8; }

        .team { flex: 1; display: flex; align-items: center; gap: 12px; font-weight: 600; font-size: 0.95rem; color: #1a1a2e; }
        .team img { width: 36px; height: 36px; object-fit: contain; border-radius: 50%; background: #f0f0f0; padding: 4px; }
        .team1 { justify-content: flex-end; text-align: right; }
        .team2 { justify-content: flex-start; text-align: left; }

        .match-center { flex: 0 0 140px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .match-status-badge { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; padding: 3px 10px; border-radius: 20px; }
        .badge-live { background: #fee2e2; color: #ef4444; }
        .badge-upcoming { background: #f3f4f6; color: #6b7280; }
        .badge-finished { background: #d1fae5; color: #059669; }
        .match-score { font-size: 1.3rem; font-weight: 800; color: #1a1a2e; letter-spacing: 2px; }
        .match-time { font-size: 1rem; font-weight: 700; color: #7c3aed; }
        .match-tournament { font-size: 0.72rem; color: #aaa; }

        /* Teams grid */
        .teams-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 16px; }
        .team-card { text-align: center; border: 1px solid #f0f0f0; padding: 16px 10px; border-radius: 10px; transition: all 0.2s; }
        .team-card:hover { border-color: #7c3aed; box-shadow: 0 2px 8px rgba(124,58,237,0.1); }
        .team-card img { width: 50px; height: 50px; object-fit: contain; margin-bottom: 8px; }
        .team-card h4 { margin: 0 0 4px; font-size: 0.85rem; color: #333; }
        .team-card span { font-size: 0.7rem; color: #aaa; background: #f3f4f6; padding: 2px 8px; border-radius: 10px; }

        /* Rankings */
        .ranking-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
        .ranking-table th { text-align: left; padding: 10px 8px; color: #888; font-size: 0.75rem; text-transform: uppercase; border-bottom: 2px solid #f0f0f0; }
        .ranking-table td { padding: 10px 8px; border-bottom: 1px solid #f8f8f8; }
        .ranking-table tr:hover td { background: #fafafa; }
        .rank-num { font-weight: 800; color: #7c3aed; }
        .team-cell { display: flex; align-items: center; gap: 8px; font-weight: 600; }
        .team-cell img { width: 22px; height: 22px; object-fit: contain; }
        .wl-badge { font-size: 0.8rem; color: #666; font-weight: 600; }

        /* Side news */
        .side-news-item { padding: 12px 0; border-bottom: 1px solid #f5f5f5; }
        .side-news-item:last-child { border-bottom: none; }
        .side-news-item a { text-decoration: none; color: #333; }
        .side-news-item a:hover .side-news-title { color: #7c3aed; }
        .side-news-title { font-weight: 600; font-size: 0.88rem; display: block; margin-bottom: 4px; line-height: 1.4; }
        .side-news-time { font-size: 0.75rem; color: #aaa; }

        /* Empty state */
        .empty-state { text-align: center; padding: 40px 20px; color: #aaa; }
        .empty-state i { font-size: 2.5rem; margin-bottom: 12px; display: block; }
    </style>
@endsection

@section('content')
<div class="tournament-page">
<div class="main-tournament-schedule">

    {{-- LEFT SIDEBAR --}}
    <aside class="left-sidebar">
        <div class="sidebar-card">
            <h3><i class="fas fa-trophy"></i> Games</h3>
            <ul>
                <li><a href="{{ url('tournament') }}" class="{{ !$gameFilter ? 'active' : '' }}"><i class="fas fa-gamepad"></i> All Games</a></li>
                <li><a href="{{ url('tournament?game=lol') }}" class="{{ $gameFilter === 'lol' ? 'active' : '' }}"><i class="fas fa-ghost"></i> League of Legends</a></li>
                <li><a href="{{ url('tournament?game=csgo') }}" class="{{ $gameFilter === 'csgo' ? 'active' : '' }}"><i class="fas fa-crosshairs"></i> CS:GO</a></li>
                <li><a href="{{ url('tournament?game=valorant') }}" class="{{ $gameFilter === 'valorant' ? 'active' : '' }}"><i class="fas fa-bullseye"></i> Valorant</a></li>
                <li><a href="{{ url('tournament?game=dota2') }}" class="{{ $gameFilter === 'dota2' ? 'active' : '' }}"><i class="fas fa-fist-raised"></i> Dota 2</a></li>
            </ul>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="schedule-content">
        {{-- Match Schedule --}}
        <div class="section-card">
            <h2><i class="fas fa-calendar-alt"></i> Match Schedule {{ $gameFilter ? '— ' . strtoupper($gameFilter) : '' }}</h2>

            <form method="GET" action="{{ url('tournament') }}" style="margin-bottom: 24px;">
                @if($gameFilter) <input type="hidden" name="game" value="{{ $gameFilter }}"> @endif
                <select name="tournament" class="filter-select" onchange="this.form.submit()">
                    <option value="">— All Tournaments —</option>
                    @foreach ($tournamentList as $t)
                        <option value="{{ $t }}" {{ $tournamentFilter === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </form>

            @if (empty($groupedMatches))
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    No matches found for the selected criteria.
                </div>
            @else
                @foreach ($groupedMatches as $date => $matches)
                <div class="match-day">
                    <div class="match-day-header"><i class="far fa-calendar"></i> {{ date('l, M d', strtotime($date)) }}</div>
                    @foreach ($matches as $m)
                    <div class="match-item {{ $m->status === 'live' ? 'live' : ($m->status === 'finished' ? 'finished' : '') }}">
                        <div class="team team1">
                            <span>{{ $m->team1_name }}</span>
                            <img src="{{ asset('img/teams/' . $m->team1_logo) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                        </div>
                        <div class="match-center">
                            @if ($m->status === 'live')
                                <span class="match-status-badge badge-live">● LIVE</span>
                                <span class="match-score">{{ $m->score_team1 }} — {{ $m->score_team2 }}</span>
                            @elseif ($m->status === 'finished')
                                <span class="match-status-badge badge-finished">✓ Finished</span>
                                <span class="match-score">{{ $m->score_team1 }} — {{ $m->score_team2 }}</span>
                            @else
                                <span class="match-status-badge badge-upcoming">Upcoming</span>
                                <span class="match-time">{{ date('H:i', strtotime($m->match_time)) }}</span>
                            @endif
                            <span class="match-tournament">{{ $m->tournament_name }}</span>
                        </div>
                        <div class="team team2">
                            <img src="{{ asset('img/teams/' . $m->team2_logo) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                            <span>{{ $m->team2_name }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            @endif
        </div>

        {{-- Teams --}}
        @if($teamsList->isNotEmpty())
        <div class="section-card">
            <h2><i class="fas fa-users"></i> Participating Teams</h2>
            <div class="teams-grid">
                @foreach ($teamsList as $team)
                <div class="team-card">
                    <img src="{{ asset('img/teams/' . $team->logo) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                    <h4>{{ $team->name }}</h4>
                    <span>{{ strtoupper($team->game_type) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </main>

    {{-- RIGHT SIDEBAR --}}
    <aside class="right-sidebar">
        <div class="sidebar-card" style="margin-bottom: 20px;">
            <h3><i class="fas fa-chart-bar"></i> Standings</h3>
            @if (empty($groupedRankings))
                <p style="color:#bbb; text-align:center; font-size:0.85rem; padding:10px 0;">No rankings available.</p>
            @else
                @foreach ($groupedRankings as $tournamentName => $teams)
                <p style="font-size:0.8rem; font-weight:700; color:#7c3aed; margin:0 0 10px;">{{ $tournamentName }}</p>
                <table class="ranking-table" style="margin-bottom:20px;">
                    <thead>
                        <tr><th>#</th><th>Team</th><th style="text-align:right;">W-L</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($teams as $team)
                        <tr>
                            <td><span class="rank-num">{{ $team->rank_position }}</span></td>
                            <td>
                                <div class="team-cell">
                                    <img src="{{ asset('img/teams/' . $team->team_logo) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                                    {{ $team->team_name }}
                                </div>
                            </td>
                            <td style="text-align:right;"><span class="wl-badge">{{ $team->wins }}-{{ $team->losses }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endforeach
            @endif
        </div>

        <div class="sidebar-card">
            <h3><i class="fas fa-bolt"></i> Tournament News</h3>
            @foreach ($sideNews as $news)
            <div class="side-news-item">
                <a href="{{ url('news/' . $news->id) }}">
                    <span class="side-news-title">{{ $news->title }}</span>
                    <span class="side-news-time"><i class="far fa-clock"></i> {{ date('M d, Y', strtotime($news->created_at)) }}</span>
                </a>
            </div>
            @endforeach
        </div>
    </aside>

</div>
</div>
@endsection
