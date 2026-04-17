<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        $gameFilter = $request->query('game');
        $tournamentFilter = $request->query('tournament');

        // 1. Tournament list for filter
        $tournamentList = DB::table('matches')
            ->whereNotNull('tournament_name')
            ->where('tournament_name', '!=', '')
            ->when($gameFilter, function ($query, $gameFilter) {
                return $query->where('game_type', $gameFilter);
            })
            ->distinct()
            ->pluck('tournament_name');

        // 2. Matches
        $matchesQuery = DB::table('matches');
        if ($gameFilter) $matchesQuery->where('game_type', $gameFilter);
        if ($tournamentFilter) $matchesQuery->where('tournament_name', $tournamentFilter);
        
        $allMatches = $matchesQuery->orderBy('match_time', 'asc')->get();

        $groupedMatches = [];
        foreach ($allMatches as $match) {
            $date = date('Y-m-d', strtotime($match->match_time));
            $groupedMatches[$date][] = $match;
        }

        // 3. Rankings
        $rankingsQuery = DB::table('team_rankings');
        if ($gameFilter) {
            $rankingsQuery->where('game_type', $gameFilter);
        } else {
            $rankingsQuery->where('game_type', 'lol');
        }
        if ($tournamentFilter) $rankingsQuery->where('tournament_name', $tournamentFilter);

        $rankings = $rankingsQuery->orderBy('rank_position', 'asc')->get();
        
        $groupedRankings = [];
        foreach ($rankings as $r) {
            $tName = $r->tournament_name ?: 'General Standings';
            $groupedRankings[$tName][] = $r;
        }

        // 4. Teams
        $teamsQuery = DB::table('teams');
        if ($gameFilter) $teamsQuery->where('game_type', $gameFilter);
        // Simplified team filtering for tournament
        $teamsList = $teamsQuery->orderBy('name', 'asc')->get();

        // 5. News
        $sideNews = DB::table('posts')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('tournament', compact(
            'gameFilter', 
            'tournamentFilter', 
            'tournamentList', 
            'groupedMatches', 
            'groupedRankings', 
            'teamsList', 
            'sideNews'
        ));
    }
}
