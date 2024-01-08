<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ResetToken;
use App\Mail\EmailSender;
use Carbon\Carbon;
use Validator;
use Auth;

class UserController extends Controller
{

    public function login() {
        return view('login');
    }

    public function authorizeUser(Request $request) {
        $this->validate($request, [
            'username'      => 'required',
            'password'      => 'required'
        ]);
        
        $user_data = array(
            'username'      => $request->get('username'),
            'password'      => $request->get('password')
        );

        if(Auth::attempt($user_data)) {
            return redirect('/');
        } else {
            return redirect()->back()->with('message', 'The username or password is incorrect.');
        }
    }

    public function logout() {
        Auth::logout();
        return redirect('/login');
    }

    public function registerUser()
    {
        return view('register');
    }

    public function storeUser(Request $request)
    {   

        $this->validate($request, [
            'name'      => 'required|',
            'username'      => 'required|unique:users|min:6',
            'email'      => 'required|unique:users|email',
            'password'      => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/',
        ],
        [
            'password.regex'    => 'Password should contain lowercase, upercase letters and digits.'
        ]);

        $user = new User();
        $names = explode(" ", $request->name);
        $user->name = $names[0];
        $user->surname = $names[1];
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->currency = "€";
        $user->email = $request->email;
        $user->admin = 0;
        $user->save();
        return redirect('/login');
    }

    public function passwordReset() {
        return view('pass-reset-request');
    }

    public function sendPasswordEmail(Request $request) {
        $this->validate($request, [
            'email'      => 'required',
        ]);
        
        $email = $request->email;
        $name = User::select([
            'users.name'
        ])
        ->where('email', $email)
        ->get();
        
        if (count($name) > 0) {
            $token = $this->generateResetToken();
            $this->storeResetToken($token, $email);
            $url = 'kvali.test/reset/password?email='.$email.'&token='.$token;
            
            $details = [
                'name'  => $name[0]['name'],
                'url'   => $url,
            ];
    
            Mail::to($email)->send(new EmailSender($details));
        }

        return redirect('/reset')->with('message', "We have emailed your password reset link!");
    }

    public function generateResetToken() {
        return Str::random(32);
    }

    public function storeResetToken($token, $email) {
        $userId = User::select([
            'users.id'
        ])
        ->where('email', $email)
        ->get()[0]['id'];
        $validUntil = Carbon::now()->addMinutes(10);

        $row = new ResetToken();
        $row->user_id = $userId;
        $row->token = $token;
        $row->valid_until = $validUntil;
        $row->save();
    }

    public function resetPassword(Request $request) {
        $email = $request->email;
        $token = $request->token;
        return view('pass-reset', compact('email', 'token'));
    }

    public function passwordResetUpdate(Request $request) {
        $this->validate($request, [
            'new_password'      => 'required|min:8|max:191|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/',
            'repeat_password'   => 'required|same:new_password',
            'email'             => 'required',
            'token'             => 'required',
        ],
        [
            'new_password.regex'    => 'Password should contain lowercase, upercase letters and digits.'
        ]);
        
        $password = $request->new_password;

        $email = $request->email;
        $token = $request->token;

        $isTokenTimeValid = $this->validatePasswordResetTime($token);
        $isTokenDataValid = $this->validatePasswordResetData($email, $token);
        
        if ($isTokenTimeValid && $isTokenDataValid) {
            $user = User::where('email', $email)->first();
            $user->password = Hash::make($password);
            $user->save();
            return redirect('/');
        }
        elseif (!$isTokenDataValid) {
            return redirect('/reset/password')->with('error', "Something went wrong!");
        }
        elseif (!$isTokenTimeValid) {
            return redirect('/reset/password')->with('error', "Password reset time has expired!");
        }
    }
    
    public function validatePasswordResetTime($token) {
        if (ResetToken::where('token', $token)->exists()) {
            $time = ResetToken::select([
                'reset_tokens.valid_until'
            ])
            ->where('token', $token)
            ->get()[0]['valid_until'];
        } else {
            return false;
        }
        
        $validUntil = Carbon::parse($time);
        $now = Carbon::now();

        if ($now->lt($validUntil)) {
            return true;
        } else {
            return false;
        }
    }

    public function validatePasswordResetData($email, $token) {
        if (User::where('email', $email)->exists()) {
            $userId = User::select([
                'users.id'
            ])
            ->where('email', $email)
            ->get()[0]['id'];
        } else {
            return false;
        }

        if (ResetToken::where('user_id', $userId)->where('token', $token)->exists()) {
            return true;
        } else {
            return false;
        }
    }

    public function editProfile() 
    {   
        $profile = User::select([
            'users.name',
            'users.surname',
            'users.username',
            'users.email',
            'users.currency'
        ])
        ->where('id', auth()->user()->id)
        ->get();

        $currencies = ['$', '€'];
        return view('profile', compact('profile', 'currencies'));
    }

    public function updateProfile(Request $request)
    {
        $userId = auth()->user()->id;
        
        $this->validate($request, [
            'name'      => 'required|min:3|max:25',
            'surname'   => 'required|min:3|max:25',
            'username'  => 'required|min:3|max:30|unique:users,username,' . $userId,
            'email'     => 'required|min:6|max:50|unique:users,email,' . $userId
        ]);

        $user = User::find(auth()->user()->id);
        $user->name = $request->get('name');
        $user->surname = $request->get('surname');
        $user->username = $request->get('username');
        $user->email = $request->get('email');
        $user->save();
        return redirect('/profile')->with('message', "Your profile information has been successfully saved");
    }

    public function updateCurrency(Request $request) {
        $user = User::find(auth()->user()->id);
        $user->currency = $request->get('selectedCurrency');
        $user->save();
        return redirect('/profile');
    }

    public function editPassword() {
        return view('change-pass');
    }
    public function updatePassword(Request $request) {

        $user = User::where('id', auth()->user()->id)->first();
        Hash::check(request('old_password'), $user->password);

        $this->validate($request, [
            'new_password'      => 'required|min:8|max:191|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/',
            'repeat_password'      => 'required|same:new_password'
        ],
        [
            'new_password.regex'    => 'Password should contain lowercase, upercase letters and digits.'
        ]);

        $user = User::find(auth()->user()->id);
        $user->password = Hash::make($request->new_password);
        $user->save();
        return redirect('/profile/change-password')->with('message', "Your password has successfully been changed.");
    }

    public function showDelete()
    {
        return view('delete-account');
    }

    public function destroyAccout(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();
        Hash::check(request('old_password'), $user->password);
        User::where('id', auth()->user()->id)->delete();
        return redirect('/');
    }
}
