<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team; 
use App\Models\User;


class TeamController extends Controller
{
    //
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'team_name' => 'required|string|max:255',
        ]);

        $team = Team::create([
            'name' => $validatedData['team_name'],
        ]);

        return response()->json(['message' => 'Team created successfully', 'team' => $team], 201);
    }

    public function listTeams()
    {
        $teams = Team::all();

        return response()->json(['teams' => $teams], 200);
    }

    
}
