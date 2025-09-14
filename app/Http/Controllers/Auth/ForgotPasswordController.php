<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SettingWebsite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $setting_web = SettingWebsite::first();
        $data = [
            'title' => __('auth.forgot_password') . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('auth.forgot_password') . ' | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Forgot Password, Reset Password, Journal, Research, OJS System',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' =>  [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('auth.forgot_password'),
                    'link' => route('password.request')
                ]
            ],
            'setting_web' => SettingWebsite::first()
        ];
        return view('front.pages.auth.forgot-password', $data);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak ditemukan dalam sistem kami'
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::where('email', $request->email)->first();

            // Generate reset token
            $token = Str::random(64);

            // Delete existing tokens for this email
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Store new token
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            // Send email
            $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

            Mail::send('front.emails.password-reset', [
                'user' => $user,
                'resetLink' => $resetLink,
                'setting_web' => SettingWebsite::first()
            ], function ($message) use ($request) {
                $setting_web = SettingWebsite::first();
                $message->to($request->email);
                $message->subject('Reset Password - ' . $setting_web->name);
                $message->from(config('mail.from.address'), $setting_web->name);
            });

            Alert::success('Success', 'Link reset password telah dikirim ke email Anda. Silakan cek email dan ikuti instruksi yang diberikan.');
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi kesalahan saat mengirim email reset password. Silakan coba lagi.');
            return redirect()->back();
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        $setting_web = SettingWebsite::first();
        $data = [
            'title' => __('auth.reset_password') . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('auth.reset_password') . ' | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Reset Password, Journal, Research, OJS System',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' =>  [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('auth.reset_password'),
                    'link' => '#'
                ]
            ],
            'setting_web' => SettingWebsite::first(),
            'token' => $token,
            'email' => $request->email
        ];
        return view('front.pages.auth.reset-password', $data);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak ditemukan dalam sistem kami',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Check if token exists and is valid
            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
                Alert::error('Error', 'Token reset password tidak valid atau sudah kadaluarsa.');
                return redirect()->route('password.request');
            }

            // Check if token is not expired (24 hours)
            if (now()->diffInHours($passwordReset->created_at) > 24) {
                Alert::error('Error', 'Token reset password sudah kadaluarsa. Silakan minta reset password baru.');
                return redirect()->route('password.request');
            }

            // Update user password
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Delete the token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            Alert::success('Success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
            return redirect()->route('login');

        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi kesalahan saat reset password. Silakan coba lagi.');
            return redirect()->back();
        }
    }
}
