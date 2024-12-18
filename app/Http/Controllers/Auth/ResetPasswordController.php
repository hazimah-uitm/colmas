<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
        // Retrieve user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and the token is valid
        if (!$user || !Password::tokenExists($user, $token)) {
            // Token is expired or invalid, redirect to the 'link-expired' page
            return redirect()->route('link-expired');
        }

        // If the token is valid, show the reset form
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email,
            'type' => $request->query('type', 'reset')
        ]);
    }
    
    protected function resetPassword($user, $password)
    {
        $user = User::find($user->id);
        $user->password = bcrypt($password);

        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
        }

        $user->remember_token = Str::random(60);
        $user->save();

        return redirect()->route('login')->with('success', 'Kata laluan telah berjaya di set. Sila log masuk menggunakan No. Pekerja dan Kata Laluan.');
    }
}
