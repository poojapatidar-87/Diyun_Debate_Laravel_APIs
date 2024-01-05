<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Import User model
use Illuminate\Support\Facades\Hash; // Import Facades to make password hashed
use Illuminate\Auth\Events\Registered;
use App\Notifications\VerifyEmailNotification; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /***  Function for USER registeration ***/

    public function register(Request $request){
        //request validtion
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'username' => 'required|unique:users'
        ]);

        // error message if email exists
        if (User::where('email', $request->email)->orWhere('username', $request->username)->first()) {
            return response([
                'message' => 'Email or Username Already Exists',
                'status' => 'failed'
            ], 200);
        }

        // Store data in table
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => \Str::random(40),
            'username' => $request->username
        ]);
        

        // Send verification email
        $user->notify(new VerifyEmailNotification($user->verification_token));



        // Create token for user
        $token = $user->createToken($request->email)->plainTextToken;

        // Response after successful registration
        return response([
            'token' => $token,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'email_verified_at' => $user->email_verified_at,
            'message' => 'Registration Successful, Please check your mailbox to verify email address',
            'status' => 'success'
        ], 201); 
    }



    /*** Function For USER Login ***/
    
    public function login(Request $request){
        //request validtion
        $request->validate([
            'email' => 'required_without:username|email',
            'password' => 'required',
            'username' => 'required_without:email'
        ]);

        // Get user email ID 
        $user = User::where('email', $request->email)->orWhere('username', $request->username)->first();

        //validate user password
        if ($user && $user->hasVerifiedEmail() && Hash::check($request->password, $user->password)) {

            // Create token for user in login
            $token = $user->createToken($request->email ?? $request->username)->plainTextToken;

            // Response after successful login
            return response([
                'token' => $token,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'email_verified_at' => $user->email_verified_at,
                'message' => 'Login Successful',
                'status' => 'success'
            ], 200);
        
        }

        // response on wrong credentials
        return response([
            'message' => 'The provided credentials are incorrect',
            'status' => 'failed'
        ], 401);
    }


    /*** Function For USER Logout ***/
    
    public function logout(Request $request){
        auth()->user()->tokens()->delete(); // delete temp login token after logout

        // Response after successful logout
        return response([
            'message' => 'Logout Successful',
            'status'=>'success'
        ], 200);
    }


    /*** Function For Getting Data of logged in USER ***/

    public function logged_user(Request $request){
        $loggeduser = auth()->user(); // validate user logged in or not

        // Response for displaying user data
        return response([
            'user' => $loggeduser,
            'message' => 'User Details',
            'status'=>'success'
        ], 200);
    }



    /*** Function to change the password when USER is logged in  ***/

    public function change_password(Request $request){

        // validate used password
        $request->validate([
            'password' => 'required|confirmed',
        ]);

        $loggeduser = auth()->user(); // validate user logged in or not 
        
        $loggeduser->password = Hash::make($request->password); // make password hashed
        $loggeduser->save();

        // Response after password changed
        return response([
            'message' => 'Password Changed Successfully',
            'status'=>'success'
        ], 200);
    }


    /**
     * Function for editing the user profile.
     */
    public function editProfile(Request $request)
    {
        // Validate the request data
        $request->validate([
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'biography' => 'nullable|string',
            'is_private_user' => 'boolean',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Update profile picture if provided
        if ($request->hasFile('profile_picture')) {
            // Store the new profile picture and update the database column
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $profilePicturePath;
        }

        // Update biography if provided
        if ($request->filled('biography')) {
            $user->biography = $request->biography;
        }

        // Update is_private_user if provided
        if ($request->filled('is_private_user')) {
            $user->is_private_user = $request->is_private_user;
        }

        // Save the changes to the user model
        $user->save();

        // Return a response with the updated user information
        return response([
            'user' => $user,
            'message' => 'Profile updated successfully',
            'status' => 'success',
        ], 200);
    }

   
}

