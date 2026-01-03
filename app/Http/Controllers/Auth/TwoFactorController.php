<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use App\Models\SettingWebsite;
use App\Models\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA method selection page.
     */
    public function selectMethod()
    {
        // Check if user is in 2FA pending state
        if (!session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        $setting_web = SettingWebsite::first();
        $data = [
            'title' => 'Verifikasi 2FA',
            'meta' => [
                'title' => 'Verifikasi 2FA | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Two Factor Authentication, 2FA, Security',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.login'),
                    'link' => route('login')
                ],
                [
                    'name' => 'Verifikasi 2FA',
                    'link' => route('2fa.select-method')
                ]
            ],
            'setting_web' => $setting_web
        ];

        return view('front.pages.auth.two-factor.select-method', $data);
    }

    /**
     * Send 2FA code via selected method.
     */
    public function sendCode(Request $request)
    {
        // Check if user is in 2FA pending state
        if (!session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        $validator = Validator::make($request->all(), [
            'method' => 'required|in:email'
        ], [
            'method.required' => 'Silakan pilih metode verifikasi',
            'method.in' => 'Metode verifikasi tidak valid'
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return redirect()->back();
        }

        $userId = session('2fa:user:id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            session()->forget(['2fa:user:id', '2fa:remember']);
            Alert::error('Error', 'Sesi telah berakhir, silakan login kembali');
            return redirect()->route('login');
        }

        // Generate 2FA code
        $twoFactorCode = TwoFactorCode::generateCode($user, $request->method);

        // Send code via email
        if ($request->method === 'email') {
            try {
                Mail::to($user->email)->send(new TwoFactorCodeMail([
                    'name' => $user->name,
                    'code' => $twoFactorCode->code
                ]));

                session(['2fa:method' => 'email']);
                Alert::success('Berhasil', 'Kode verifikasi telah dikirim ke email Anda');
            } catch (\Exception $e) {
                Alert::error('Error', 'Gagal mengirim kode verifikasi. Silakan coba lagi.');
                return redirect()->back();
            }
        }

        return redirect()->route('2fa.verify');
    }

    /**
     * Show the 2FA verification page.
     */
    public function showVerifyForm()
    {
        // Check if user is in 2FA pending state
        if (!session()->has('2fa:user:id') || !session()->has('2fa:method')) {
            return redirect()->route('login');
        }

        $userId = session('2fa:user:id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            session()->forget(['2fa:user:id', '2fa:remember', '2fa:method']);
            return redirect()->route('login');
        }

        $setting_web = SettingWebsite::first();
        $maskedEmail = $this->maskEmail($user->email);

        $data = [
            'title' => 'Verifikasi Kode 2FA',
            'meta' => [
                'title' => 'Verifikasi Kode 2FA | ' . $setting_web->name,
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Two Factor Authentication, 2FA, Security',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.login'),
                    'link' => route('login')
                ],
                [
                    'name' => 'Verifikasi Kode',
                    'link' => route('2fa.verify')
                ]
            ],
            'setting_web' => $setting_web,
            'masked_email' => $maskedEmail,
            'method' => session('2fa:method')
        ];

        return view('front.pages.auth.two-factor.verify', $data);
    }

    /**
     * Verify the 2FA code.
     */
    public function verify(Request $request)
    {
        // Check if user is in 2FA pending state
        if (!session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6'
        ], [
            'code.required' => 'Kode verifikasi tidak boleh kosong',
            'code.size' => 'Kode verifikasi harus 6 digit'
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return redirect()->back();
        }

        $userId = session('2fa:user:id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            session()->forget(['2fa:user:id', '2fa:remember', '2fa:method']);
            Alert::error('Error', 'Sesi telah berakhir, silakan login kembali');
            return redirect()->route('login');
        }

        // Find valid code
        $twoFactorCode = TwoFactorCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$twoFactorCode) {
            Alert::error('Error', 'Kode verifikasi tidak valid atau sudah kadaluarsa');
            return redirect()->back();
        }

        // Mark code as used
        $twoFactorCode->update(['is_used' => true]);

        // Log the user in
        Auth::login($user, session('2fa:remember', false));

        // Clear 2FA session data
        session()->forget(['2fa:user:id', '2fa:remember', '2fa:method']);

        // Set 2FA verified session
        session(['2fa:verified' => true]);

        Alert::success('Berhasil', 'Login berhasil');

        // Redirect based on role
        if ($user->hasRole('super-admin|keuangan|editor|humas')) {
            return redirect()->intended(route('back.dashboard'));
        }

        return redirect()->intended('/');
    }

    /**
     * Resend the 2FA code.
     */
    public function resend()
    {
        // Check if user is in 2FA pending state
        if (!session()->has('2fa:user:id') || !session()->has('2fa:method')) {
            return redirect()->route('login');
        }

        $userId = session('2fa:user:id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            session()->forget(['2fa:user:id', '2fa:remember', '2fa:method']);
            Alert::error('Error', 'Sesi telah berakhir, silakan login kembali');
            return redirect()->route('login');
        }

        $method = session('2fa:method');

        // Generate new code
        $twoFactorCode = TwoFactorCode::generateCode($user, $method);

        // Send code via email
        if ($method === 'email') {
            try {
                Mail::to($user->email)->send(new TwoFactorCodeMail([
                    'name' => $user->name,
                    'code' => $twoFactorCode->code
                ]));

                Alert::success('Berhasil', 'Kode verifikasi baru telah dikirim ke email Anda');
            } catch (\Exception $e) {
                Alert::error('Error', 'Gagal mengirim kode verifikasi. Silakan coba lagi.');
            }
        }

        return redirect()->back();
    }

    /**
     * Cancel 2FA verification and go back to login.
     */
    public function cancel()
    {
        session()->forget(['2fa:user:id', '2fa:remember', '2fa:method']);
        return redirect()->route('login');
    }

    /**
     * Mask email for display.
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 4, 2)) . substr($name, -2);

        return $maskedName . '@' . $domain;
    }
}
