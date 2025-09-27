<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SettingWebsite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class RegisterController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();
        $data = [
            'title' => __('auth.register') . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('auth.register') . ' | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Register, Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' =>  [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('auth.register'),
                    'link' => route('register')
                ]
            ],
            'setting_web' => SettingWebsite::first()
        ];
        return view('front.pages.auth.register', $data);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'sinta_id' => 'nullable|string|max:255',
            'scopus_id' => 'nullable|string|max:255',
            'google_scholar' => 'nullable|string|max:255',
            'agree_terms' => 'required|accepted',
        ], [
            'name.required' => 'Nama lengkap tidak boleh kosong',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'agree_terms.required' => 'Anda harus menyetujui Syarat dan Ketentuan serta Kebijakan Privasi',
            'agree_terms.accepted' => 'Anda harus menyetujui Syarat dan Ketentuan serta Kebijakan Privasi',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'sinta_id' => $request->sinta_id,
                'scopus_id' => $request->scopus_id,
                'google_scholar' => $request->google_scholar,
            ]);

            // Note: No default role assignment since only admin roles exist in this system
            // Users will be assigned roles by administrators if needed

            Alert::success('Success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
            return redirect()->route('login');

        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }
}
