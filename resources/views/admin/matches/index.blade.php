@extends('layouts.admin')
@section('title', 'Manage Esports Matches')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Match Schedule</h3>
        <a href="{{ route('admin.matches.create') }}" class="btn" style="background:var(--admin-accent); color:white; border:none; padding:10px 20px; border-radius:5px; text-decoration:none;">+ Add New Match</a>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:15px;">Tournament</th>
                <th style="padding:15px;">Matchup</th>
                <th style="padding:15px;">Time</th>
                <th style="padding:15px;">Status</th>
                <th style="padding:15px;">Score</th>
                <th style="padding:15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matches as $match)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:15px;">
                    <div style="font-weight:600;">{{ $match->tournament_name }}</div>
                    <span class="status-badge" style="background:#eee; color:#666; font-size:0.7rem;">{{ strtoupper($match->game_type) }}</span>
                </td>
                <td style="padding:15px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span>{{ $match->team1_name }}</span>
                        <span style="color:#999; font-size:0.8rem;">VS</span>
                        <span>{{ $match->team2_name }}</span>
                    </div>
                </td>
                <td style="padding:15px; color:#666;">{{ date('M d, H:i', strtotime($match->match_time)) }}</td>
                <td style="padding:15px;"><span class="status-badge status-{{ $match->status }}">{{ $match->status }}</span></td>
                <td style="padding:15px; font-weight:bold;">{{ $match->score_team1 }} - {{ $match->score_team2 }}</td>
                <td style="padding:15px;">
                    <a href="{{ route('admin.matches.edit', $match->id) }}" style="color:#3498db; margin-right:15px;"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.matches.destroy', $match->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this match?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none; border:none; color:#e74c3c; cursor:pointer;"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:20px;">
        {{ $matches->links() }}
    </div>
</div>
@endsection
