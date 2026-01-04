<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class ContactController extends Controller
{
    public function index()
    {
        $setting_web = SettingWebsite::first();
        $data = [
            'title' => __('front.contact_us') . ' | ' . $setting_web->name,
            'meta' => [
                'title' => __('front.contact_us') . ' | ' . $setting_web->name,
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
                    'name' => __('front.contact_us'),
                    'link' => route('contact.index')
                ]
                ],
            'setting_web' => SettingWebsite::first()
        ];
        return view('front.pages.home.contact', $data);
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'subject' => 'required',
            'message' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'subject.required' => 'Subjek tidak boleh kosong',
            'message.required' => 'Pesan tidak boleh kosong',
            'g-recaptcha-response.required' => 'Captcha harus diisi',
            'g-recaptcha-response.captcha' => 'Captcha tidak valid',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', 'Please fill all the form');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $message = new Message();
        $message->name = $request->name;
        $message->email = $request->email;
        $message->phone = $request->phone;
        $message->subject = $request->subject;
        $message->message = $request->message;
        $message->save();

        Alert::success('Success', 'Message has been sent');
        return redirect()->back();


    }
}
