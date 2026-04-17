@extends('layouts.admin')
@section('title', 'Add New Match')
@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <form action="{{ route('admin.matches.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display:block; margin-bottom:5px;">Tournament Name</label>
                <input type="text" name="tournament_name" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Game Type</label>
                <select name="game_type" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="lol">League of Legends</option>
                    <option value="valorant">VALORANT</option>
                    <option value="dota">DOTA 2</option>
                    <option value="csgo">CS:GO</option>
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Match Time</label>
                <input type="datetime-local" name="match_time" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Status</label>
                <select name="status" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="upcoming">Upcoming</option>
                    <option value="live">Live</option>
                    <option value="finished">Finished</option>
                </select>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 20px; padding: 20px; background: #f9f9f9; border-radius: 10px;">
            <div>
                <h4 style="margin-top:0;">Team 1</h4>
                <div style="margin-bottom:10px;">
                    <label style="display:block; margin-bottom:5px;">Team Name</label>
                    <input type="text" name="team1_name" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
                </div>
                <div style="margin-bottom:10px;">
                    <label style="display:block; margin-bottom:5px;">Logo</label>
                    <input type="file" name="team1_logo">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px;">Score</label>
                    <input type="number" name="score_team1" value="0" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
            </div>
            <div>
                <h4 style="margin-top:0;">Team 2</h4>
                <div style="margin-bottom:10px;">
                    <label style="display:block; margin-bottom:5px;">Team Name</label>
                    <input type="text" name="team2_name" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
                </div>
                <div style="margin-bottom:10px;">
                    <label style="display:block; margin-bottom:5px;">Logo</label>
                    <input type="file" name="team2_logo">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px;">Score</label>
                    <input type="number" name="score_team2" value="0" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display:block; margin-bottom:5px;">Stream Link (Twitch/YouTube)</label>
            <input type="text" name="stream_link" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="https://...">
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.matches') }}" style="background:#eee; color:#333; text-decoration:none; padding:12px 25px; border-radius:5px;">Cancel</a>
            <button type="submit" style="background:var(--admin-accent); color:white; border:none; padding:12px 30px; border-radius:5px; cursor:pointer; font-weight:bold;">Add Match</button>
        </div>
    </form>
</div>
@endsection
