<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
     /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'google_id' => $googleUser->id,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'photo' => $googleUser->avatar,
                'password' => Hash::make(now()) // Password bisa di-generate secara acak
            ]);

            Auth::login($user);

            // Jika user memiliki peran tertentu, redirect ke dashboard
            if ($user->hasRole('super-admin|keuangan|editor|humas')) {
                return redirect()->intended(route('back.dashboard'));
            }
            return redirect()->intended('/'); // Ganti dengan rute yang sesuai setelah login berhasil

        } catch (\Throwable $th) {
            // Tangani error, misalnya redirect ke halaman login dengan pesan error
            return redirect('/login')->with('error', 'Something went wrong!');
        }
    }
}
