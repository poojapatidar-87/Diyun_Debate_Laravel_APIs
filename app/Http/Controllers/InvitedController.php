<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation; 
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\URL;


class InvitedController extends Controller
{
    public function showInvitation(Request $request, $token)
    {

        // Parse the token and retrieve team_id and token
        list($teamId, $invitationToken) = explode('-', $token);

        // Validate the invitation
        $invitation = Invitation::where('team_id', $teamId)
            ->where('token', $invitationToken)
            ->first();

        if (!$invitation) {
            // Handle invalid or expired invitation
            return redirect()->route('login')->with('error', 'Invalid or expired invitation link');
        }
       
        // Check if the user is already registered
        if (!is_null($invitation->email)) {
            $user = User::where('email', $invitation->email)->first();
    
            if ($user) {
                // User is registered, add to the team
                $team = Team::find($teamId);
                $userName = $user->username;
                $team->users()->attach($user->id, ['role' => $invitation->role, 'username' => $userName]);
                // Delete the used invitation token
                $invitation->delete();
    
                // Redirect to team dashboard or wherever you want
                return redirect()->route('team.dashboard')->with('success', 'You have joined the team successfully');
            }
        }

        // User is not registered, redirect to register page with invitation data
        return redirect()->route('register')->with('invitation_data', $invitation);
    }


    public function showJoinTeam(Request $request, $teamId, $token)
    {
        // Verify the signed URL
        if (!URL::hasValidSignature($request)) {
            abort(403, 'Invalid or expired link');
        }
        
        // Validate the team
        $team = Team::find($teamId);

        if (!$team) {
            abort(404, 'Team not found');
        }

        // Check if the user is authenticated
    if ($request->user()) {
        // User is registered, add to the team
        $user = User::where('email', $request->user()->email)->first();

        if ($user) {
            $team->users()->attach($user->id, ['role' => 'Member', 'username' => $user->username]);
            // Redirect to team dashboard or wherever you want
            // return redirect()->route('team.dashboard')->with('success', 'You have joined the team successfully');
        }
    }

        // User is not registered, redirect to register page with team data
        // return redirect()->route('register')->with('team_data', $team);
    }
}
