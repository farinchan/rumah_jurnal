<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\EventUser;
use App\Models\SettingWebsite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class AccountController extends Controller
{
    public function profile(Request $request)
    {
        $setting_web = SettingWebsite::first();
        $me = Auth::user();
        if (!$me) {
            return redirect()->route('login')->with('error', "You must be logged in to view your profile.");
        }

        $data = [
            'title' => $me->name . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('front.profile'),
                'description' => strip_tags($setting_web->about),
                'keywords' => $setting_web->name . ', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => __('front.profile'),
                    'link' => route('account.profile')
                ]
            ],
            'setting_web' => $setting_web,
            'events' => EventUser::with(['event'])
                ->where('user_id', $me->id)
                ->latest()
                ->paginate(10),
            'me' => $me,
        ];
        // return response()->json($data);
        return view('front.pages.account.profile', $data);
    }

    public function updateProfile(Request $request)
    {
        $authUser = Auth::user();

        if (!$authUser) {
            Alert::error('Error', 'You must be logged in to update your profile.');
            return redirect()->route('login');
        }

        // Get the user model explicitly
        $user = User::find($authUser->id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:laki-laki,perempuan',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
            'new_password_confirmation' => 'nullable|string|min:8',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'gender.required' => 'Jenis kelamin wajib dipilih',
            'gender.in' => 'Jenis kelamin tidak valid',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar yang diizinkan: jpeg, png, jpg, gif',
            'photo.max' => 'Ukuran gambar maksimal 2MB',
            'new_password.min' => 'Password minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak sama',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Update basic profile data
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->gender = $request->gender;

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($user->photo && Storage::exists('public/' . $user->photo)) {
                    Storage::delete('public/' . $user->photo);
                }

                // Store new photo
                $photoPath = $request->file('photo')->store('users', 'public');
                $user->photo = $photoPath;
            }

            // Handle password change
            if ($request->filled('current_password') && $request->filled('new_password')) {
                // Verify current password
                if (!Hash::check($request->current_password, $user->password)) {
                    Alert::error('Error', 'Password saat ini tidak benar');
                    return redirect()->back()->withInput();
                }

                // Update password
                $user->password = Hash::make($request->new_password);
            }

            $user->save();

            Alert::success('Success', 'Profil berhasil diperbarui');
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
