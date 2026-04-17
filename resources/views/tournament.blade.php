@extends('layouts.app')

@section('title', 'Tournament - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tournament.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ranking.css') }}">
    <style>
        .main-tournament-schedule { display: grid; grid-template-columns: 220px 1fr 300px; gap: 30px; margin: 30px auto; max-width: 1400px; padding: 0 20px; }
        @media (max-width: 1200px) { .main-tournament-schedule { grid-template-columns: 200px 1fr; } .right-sidebar { display: none; } }
        @media (max-width: 768px) { .main-tournament-schedule { grid-template-columns: 1fr; } .left-sidebar { display: none; } }
        .sidebar h3 { font-size: 1.1rem; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; text-transform: uppercase; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin-bottom: 10px; }
        .sidebar ul li a { text-decoration: none; color: #666; display: flex; align-items: center; gap: 10px; transition: 0.3s; padding: 8px 15px; border-radius: 5px; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #f0f0f5; color: var(--accent-color); font-weight: 600; }
        .schedule-content { min-width: 0; }
        .tournament-section { background: white; padding: 30px; border-radius: 10px; margin-bottom: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .tournament-section h2 { margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .match-day { margin-bottom: 30px; }
        .match-day h3 { font-size: 1rem; color: #333; background: #f8f9fa; padding: 8px 15px; border-radius: 5px; margin-bottom: 15px; }
        .matches { list-style: none; padding: 0; }
        .match-item { display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #f5f5f5; transition: 0.2s; border-radius: 8px; }
        .match-item:hover { background: #fcfcff; }
        .match-item.live { background: #fff5f5; border-left: 4px solid #ff4757; }
        .team { flex: 1; display: flex; align-items: center; gap: 15px; font-weight: 500; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team1 { justify-content: flex-end; text-align: right; }
        .match-details { flex: 0 0 160px; text-align: center; display: flex; flex-direction: column; }
        .match-status { font-size: 0.75rem; font-weight: bold; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; margin-bottom: 5px; }
        .status-live { color: #ff4757; background: rgba(255, 71, 87, 0.1); }
        .status-upcoming { color: #555; background: #eee; }
        .status-finished { color: #4CAF50; background: rgba(76, 175, 80, 0.1); }
        .score { font-size: 1.2rem; font-weight: bold; }
        .teams-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 20px; }
        .team-card { text-align: center; border: 1px solid #eee; padding: 15px; border-radius: 10px; }
        .team-card img { width: 60px; height: 60px; object-fit: contain; margin-bottom: 10px; }
        .ranking-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        .ranking-table th { text-align: left; background: #f8f9fa; padding: 10px; color: #555; border-bottom: 2px solid #eee; }
        .ranking-table td { padding: 12px 10px; border-bottom: 1px solid #eee; }
        .team-cell { display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .team-cell img { width: 25px; height: 25px; object-fit: contain; }
        .side-news-list { list-style: none; padding: 0; }
        .side-news-item { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .side-news-item a { text-decoration: none; color: #333; display: block; }
        .side-news-title { font-weight: 500; font-size: 0.95rem; display: block; margin-bottom: 5px; }
        .side-news-time { font-size: 0.8rem; color: #999; }
    </style>
@endsection

@section('content')
<div class="main-tournament-schedule">
    <aside class="sidebar left-sidebar">
        <h3><i class="fas fa-trophy"></i> Games</h3>
        <ul>
            <li><a href="{{ url('tournament') }}" class="{{ !$gameFilter ? 'active' : '' }}"><i class="fas fa-gamepad"></i> All Games</a></li>
            <li><a href="{{ url('tournament?game=lol') }}" class="{{ $gameFilter === 'lol' ? 'active' : '' }}"><i class="fas fa-ghost"></i> League of Legends</a></li>
            <li><a href="{{ url('tournament?game=csgo') }}" class="{{ $gameFilter === 'csgo' ? 'active' : '' }}"><i class="fas fa-crosshairs"></i> CS:GO</a></li>
            <li><a href="{{ url('tournament?game=valorant') }}" class="{{ $gameFilter === 'valorant' ? 'active' : '' }}"><i class="fas fa-bullseye"></i> Valorant</a></li>
            <li><a href="{{ url('tournament?game=dota2') }}" class="{{ $gameFilter === 'dota2' ? 'active' : '' }}"><i class="fas fa-fist-raised"></i> Dota 2</a></li>
        </ul>
    </aside>

    <main class="schedule-content">
        <div class="tournament-section">
            <h2><i class="fas fa-calendar-alt"></i> Match Schedule {{ $gameFilter ? '- ' . strtoupper($gameFilter) : '' }}</h2>
            
            <div class="filter-container" style="margin-bottom: 30px;">
                <form method="GET" action="{{ url('tournament') }}">
                    @if($gameFilter) <input type="hidden" name="game" value="{{ $gameFilter }}"> @endif
                    <select name="tournament" onchange="this.form.submit()" style="padding: 10px 15px; border-radius: 5px; border: 1px solid #ddd; width: 100%; max-width: 300px;">
                        <option value="">-- All Tournaments --</option>
                        @foreach ($tournamentList as $t)
                            <option value="{{ $t }}" {{ $tournamentFilter === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if (empty($groupedMatches))
                <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 10px;">No matches found for the selected criteria.</div>
            @else
                @foreach ($groupedMatches as $date => $matches)
                <div class="match-day">
                    <h3>{{ date('l, M d', strtotime($date)) }}</h3>
                    <ul class="matches">
                        @foreach ($matches as $m)
                        <li class="match-item {{ $m->status === 'live' ? 'live' : '' }}">
                            <div class="team team1">
                                <span>{{ $m->team1_name }}</span>
                                <img src="{{ asset('img/teams/' . $m->team1_logo) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                            </div>
                            <div class="match-details">
                                @if ($m->status === 'live')
                                    <span class="match-status status-live">● LIVE</span>
                                    <span class="score">{{ $m->score_team1 }} - {{ $m->score_team2 }}</span>
                                @elseif ($m->status === 'finished')
                                    <span class="match-status status-finished">Finished</span>
                                    <span class="score">{{ $m->score_team1 }} - {{ $m->score_team2 }}</span>
                                @else
                                    <span class="match-status status-upcoming">Upcoming</span>
                                    <span style="font-weight:bold;">{{ date('H:i', strtotime($m->match_time)) }}</span>
                                @endif
                                <span style="font-size: 0.75rem; color: #999; margin-top: 5px;">{{ $m->tournament_name }}</span>
                            </div>
                            <div class="team team2">
                                <img src="{{ asset('img/teams/' . $m->team2_logo) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                                <span>{{ $m->team2_name }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            @endif
        </div>

        <div class="tournament-section">
            <h2><i class="fas fa-users"></i> Participating Teams</h2>
            <div class="teams-grid">
                @foreach ($teamsList as $team)
                <div class="team-card">
                    <img src="{{ asset('img/teams/' . $team->logo) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                    <h4 style="margin: 0; font-size: 0.9rem;">{{ $team->name }}</h4>
                    <span style="font-size: 0.7rem; color: #999;">{{ strtoupper($team->game_type) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </main>

    <aside class="sidebar right-sidebar">
        @if (empty($groupedRankings))
            <h3><i class="fas fa-chart-bar"></i> Standings</h3>
            <p style="color: #999; text-align: center;">No rankings available.</p>
        @else
            @foreach ($groupedRankings as $tournamentName => $teams)
                <h3><i class="fas fa-chart-bar"></i> Standings: {{ $tournamentName }}</h3>
                <table class="ranking-table" style="margin-bottom: 30px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Team</th>
                            <th style="text-align: right;">W-L</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teams as $team)
                        <tr>
                            <td style="font-weight:bold; color:#555;">{{ $team->rank_position }}</td>
                            <td>
                                <div class="team-cell">
                                    <img src="{{ asset('img/teams/' . $team->team_logo) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                                    <span>{{ $team->team_name }}</span>
                                </div>
                            </td>
                            <td style="text-align: right; color:#777;">{{ $team->wins }}-{{ $team->losses }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endif

        <h3><i class="fas fa-bolt"></i> Tournament News</h3>
        <ul class="side-news-list">
            @foreach ($sideNews as $news)
            <li class="side-news-item">
                <a href="{{ url('news/' . $news->id) }}">
                    <span class="side-news-title">{{ $news->title }}</span>
                    <span class="side-news-time"><i class="far fa-clock"></i> {{ date('M d, Y', strtotime($news->created_at)) }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </aside>
</div>
@endsection
