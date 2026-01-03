<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();
        $data = [
            'title' => __('front.login') . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('front.login') . ' | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Contact Us, Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' =>  [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.login'),
                    'link' => route('login')
                ]
            ],
            'setting_web' => SettingWebsite::first()
        ];
        return view('front.pages.auth.login', $data);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required|min:6'
        ], [
            'login.required' => 'Email atau username tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter'
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginType => $request->input('login'), 'password' => $request->input('password')], false)) {
            $user = Auth::user();

            // Check if user has admin roles that require 2FA
            if ($user->hasRole('super-admin|keuangan|editor|humas')) {
                // Logout the user temporarily and store user id in session for 2FA
                Auth::logout();
                session(['2fa:user:id' => $user->id]);
                session(['2fa:remember' => $request->has('remember')]);

                return redirect()->route('2fa.select-method');
            }

            // For regular users, login directly
            return redirect()->intended('/');
        }

        Alert::error('Error', 'Email atau username dan password salah');
        return redirect()->back()->withInput();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
