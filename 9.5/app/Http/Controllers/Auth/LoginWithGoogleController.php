<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class LoginWithGoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $finduser = User::where('social_id', $user['id'])->first();
            if ($finduser) {
                auth()->login($finduser);
                return redirect('/home');
            } else {
                $newUser = User::create([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'social_id' => $user['id'],
                    'social_type' => 'google',
                    'password' => bcrypt(uniqid())
                ]);
                auth()->login($newUser);
                return redirect('/home');
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return redirect()->back()->withErrors('Something went wrong');
        }
    }
}
