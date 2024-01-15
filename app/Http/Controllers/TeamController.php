<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\InvitedController;

class TeamController extends Controller
{
    //
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'team_name' => 'required|string|max:255',
        ]);

        // Generate a unique token using Laravel's Str::random()
        $token = Str::random(32); // You can adjust the length as needed

        $team = Team::create([
            'name' => $validatedData['team_name'],
            'token' => $token,
        ]);

        return response()->json(['message' => 'Team created successfully', 'team' => $team], 201);
    }

    public function inviteMember(Request $request, Team $team)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:Member,Admin',
            'invite_message' => 'nullable|string',
            'notify_by_email' => 'boolean',
        ]);

        // Generate a unique token for the invitation
        $token = Str::random(32);

        // Create the invitation
        $invitation = $team->invitations()->create(array_merge($validatedData, ['token' => $token]));
        $invitation->team_id = $team->id;
        $invitation->save();

        // Send the email with the invitation link
        // Use your mail sending logic here, e.g., Mail::to($validatedData['email'])->send(...);
        $invitationLink = route('invitations.show', ['token' => $team->id . '-' . $invitation->token]);
        Mail::to($validatedData['email'])->send(new InvitationMail($team->name, $invitationLink));

        return response()->json(['message' => 'Invitation sent successfully', 'invitation' => $invitation], 201);
    }

    // generate token for the inivite link
    public function generateLink(Team $team)
    {
        $token = Str::random(32); // Generate a unique token for the link
        // You can customize the route and parameters based on your application structure
        $url = config('app.url') . '/invited?token=' . $team->id . '-' . $token;
        return $url;
    }

    // get all the team members
    public function getTeamMembers(Team $team)
    {
        $teamMembers = \DB::table('users')
            ->select('users.username', 'team_user.role')
            ->join('team_user', 'users.id', '=', 'team_user.user_id')
            ->where('team_user.team_id', $team->id)
            ->get();

        return response()->json(['members' => $teamMembers], 200);
    }

    // get team members count
    public function getTeamMemberCount(Team $team)
    {
        $memberCount = $team->users()->count();
        return response()->json(['member_count' => $memberCount], 200);
    }

    // search for team members
    public function searchTeamMembers(Request $request, Team $team)
    {
        $searchQuery = $request->query('q');

        $teamMembers = \DB::table('users')
            ->select('users.username', 'team_user.role')
            ->join('team_user', 'users.id', '=', 'team_user.user_id')
            ->where('team_user.team_id', $team->id)
            ->where('users.username', 'like', '%' . $searchQuery . '%')
            ->get();

        if ($teamMembers->isEmpty()) {
            return response()->json(['message' => 'No team members found for the given search query'], 404);
        }

        return response()->json(['members' => $teamMembers], 200);
    }

    // Generate a unique copyable link to join the team
    public function generateCopyableLink(Team $team)
    {
        $token = Str::random(32); // Generate a unique token for the link

        // Create a signed URL with the team ID and token
        $signedUrl = URL::signedRoute('join-team', ['teamId' => $team->id, 'token' => $token]);

        return response()->json(['copyable_link' => $signedUrl], 200);
    }

}
