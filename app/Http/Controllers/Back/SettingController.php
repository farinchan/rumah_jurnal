<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class SettingController extends Controller
{
    public function website()
    {
        $data = [
            'title' => 'Setting Website',
            'menu' => 'Setting',
            'sub_menu' => 'Website',
            'setting' => SettingWebsite::first(),
        ];
        return view('back.pages.setting.index', $data);
    }

    public function websiteUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'favicon' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            'twitter' => 'nullable',
            'youtube' => 'nullable',
            'whatsapp' => 'nullable',
            'telegram' => 'nullable',
            'linkedin' => 'nullable',
            'about' => 'nullable',
        ]);

        // dd($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->all());
        }

        $setting = SettingWebsite::first();
        $setting->name = $request->name;
        $setting->email = $request->email;
        $setting->phone = $request->phone;
        $setting->address = $request->address;
        $setting->latitude = $request->latitude;
        $setting->longitude = $request->longitude;
        $setting->facebook = $request->facebook;
        $setting->instagram = $request->instagram;
        $setting->twitter = $request->twitter;
        $setting->youtube = $request->youtube;
        $setting->whatsapp = $request->whatsapp;
        $setting->telegram = $request->telegram;
        $setting->linkedin = $request->linkedin;
        $setting->about = $request->about;

        if ($request->hasFile('logo')) {
            Storage::delete('public/' . $setting->logo);
            $logo = $request->file('logo');
            $logoPath = $logo->storeAs('setting', 'logo.' . $logo->getClientOriginalExtension(), 'public');
            $setting->logo = str_replace('public/', '', $logoPath);
        }

        if ($request->hasFile('favicon')) {
            Storage::delete('public/' . $setting->favicon);
            $favicon = $request->file('favicon');
            $faviconPath = $favicon->storeAs('setting', 'favicon.' . $favicon->getClientOriginalExtension(), 'public');
            $setting->favicon = str_replace('public/', '', $faviconPath);
        }

        $setting->save();

        return redirect()->back()->with('success', 'Setting website berhasil diperbarui');
    }

    public function informationUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'terms_conditions' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->all());
        }

        $setting = SettingWebsite::first();
        $setting->terms_conditions = $request->terms_conditions;
        $setting->save();

        return redirect()->back()->with('success', 'Informasi berhasil diperbarui');
    }
}
