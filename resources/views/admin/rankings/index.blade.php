@extends('layouts.admin')
@section('title', 'Manage Esports Rankings')
@section('content')
<div style="display:grid; grid-template-columns: 1fr 350px; gap: 30px;">
    <div class="card">
        <h3 style="margin-top:0;">Team Standings</h3>
        @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                    <th style="padding:15px;">Game</th>
                    <th style="padding:15px;">Rank</th>
                    <th style="padding:15px;">Team</th>
                    <th style="padding:15px;">Record</th>
                    <th style="padding:15px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankings as $rank)
                <tr style="border-bottom:1px solid #f5f5f5;">
                    <td style="padding:15px;"><span class="status-badge" style="background:#eee; color:#666; font-size:0.7rem;">{{ strtoupper($rank->game_type) }}</span></td>
                    <td style="padding:15px; font-weight:bold;">#{{ $rank->rank_position }}</td>
                    <td style="padding:15px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="{{ asset('img/teams/'.$rank->team_logo) }}" style="width:30px; height:30px; object-fit:contain;" onerror="this.style.display='none'">
                            <span>{{ $rank->team_name }}</span>
                        </div>
                    </td>
                    <td style="padding:15px;">{{ $rank->wins }}W - {{ $rank->losses }}L</td>
                    <td style="padding:15px;">
                        <a href="{{ route('admin.rankings.destroy', $rank->id) }}" onclick="return confirm('Delete?')" style="color:#e74c3c;"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Add New Ranking</h3>
        <form action="{{ route('admin.rankings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Game Type</label>
                <select name="game_type" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
                    <option value="lol">League of Legends</option>
                    <option value="valorant">VALORANT</option>
                    <option value="dota">DOTA 2</option>
                    <option value="csgo">CS:GO</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Team Name</label>
                <input type="text" name="team_name" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Rank Position</label>
                <input type="number" name="rank_position" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:15px;">
                <div>
                    <label style="display:block; margin-bottom:5px;">Wins</label>
                    <input type="number" name="wins" value="0" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px;">Losses</label>
                    <input type="number" name="losses" value="0" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px;">Team Logo (Optional)</label>
                <input type="file" name="team_logo" style="width:100%;">
            </div>
            <button type="submit" style="width:100%; background:var(--admin-sidebar); color:white; border:none; padding:12px; border-radius:5px; cursor:pointer; font-weight:bold;">Save Ranking</button>
        </form>
    </div>
</div>
@endsection
