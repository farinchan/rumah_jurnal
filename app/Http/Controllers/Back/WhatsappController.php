<?php

namespace App\Http\Controllers\back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class WhatsappController extends Controller
{
    public function setting()
    {
        $data = [
            'title' => 'Pengaturan Whatsapp',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Pengaturan',
                    'link' => route('back.whatsapp.setting')
                ]
            ],
        ];

        return view('back.pages.whatsapp.setting', $data);
    }

    public function message()
    {
        $data = [
            'title' => 'Pesan Whatsapp',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Pesan Whatsapp',
                    'link' => route('back.whatsapp.message')
                ]
            ],
        ];

        return view('back.pages.whatsapp.message', $data);
    }

    public function sendMessage(Request $request)
    {
        $data = [
            'title' => 'Documentation',
            'breadcrumbs' => [
                [
                    'name' => 'Home',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Message',
                ],
                [
                    'name' => 'Send Message',
                    'link' => route('back.whatsapp.message.sendMessage')
                ],
            ],
        ];

        return view('back.pages.whatsapp.message.send-message', $data);
    }

    public function sendImage(Request $request)
    {
        $data = [
            'title' => 'Documentation',
            'breadcrumbs' => [
                [
                    'name' => 'Home',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Message',
                ],
                [
                    'name' => 'Send Image',
                    'link' => route('back.whatsapp.message.sendImage')
                ],
            ],

        ];

        return view('back.pages.whatsapp.message.send-image', $data);
    }

    public function sendImageProcess(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'phone' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'message' => 'nullable|string',
            ],
            [

                'phone.required' => 'Phone number is required',
                'image.required' => 'Image is required',
                'image.image' => 'The file must be an image',
                'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg',
                'image.max' => 'The image may not be greater than 2MB',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->first());
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '_' . Auth::id() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('upload', $fileName, 'public');
        }

        try {
            $response = Http::post(env('WHATSAPP_API_URL')  . "/send-image", [
                'session' => env('WHATSAPP_API_SESSION'), // Use the session name from your environment variable
                'to' => $request->phone,
                // 'urlImage' => Storage::url($imagePath),
                'urlImage' => "https://upload.wikimedia.org/wikipedia/id/b/b0/Kamen_rider_eurodata.png",
                'caption' => $request->message
            ]);

            if ($response->status() != 200) {

                Alert::error('Error', 'Failed to send image: ' . $response->json()['message'] ?? 'Unknown error');
                return  redirect()->back()->with('error', 'Failed to send image: ' . $response->json()['message'] ?? 'Unknown error');
            }
            Alert::success('Success', 'Image sent successfully');
            return redirect()->back()->with('success', 'Image sent successfully');
        } catch (\Throwable $th) {
            // If there is an error, you can redirect back with an error message
            Alert::error('An error occurred: ' . $th->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $th->getMessage());
        }


        return redirect()->back()->with('success', 'Image sent successfully');
    }


    public function sendBulkMessage(Request $request)
    {
        $data = [
            'title' => 'Documentation',
            'breadcrumbs' => [
                [
                    'name' => 'Home',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Message',
                ],
                [
                    'name' => 'Send Bulk Message',
                    'link' => route('back.whatsapp.message.sendBulkMessage')
                ],
            ],

        ];

        return view('back.pages.whatsapp.message.send-bulk-message', $data);
    }

    public function sendBulkMessageProcess(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'delay' => 'nullable|integer|min:1000',
                'phones' => 'required|array',
                'message' => 'required|string',
            ],
            [

                'delay.integer' => 'Delay must be an integer',
                'delay.min' => 'Delay must be at least 1000 milliseconds',
                'phone.required' => 'Phone numbers are required',
                'phone.array' => 'Phone numbers must be an array',
                'phone.*.required' => 'Each phone number is required',
                'message.required' => 'Message is required',
            ]
        );
        // dd($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->first());
        }

        try {
            $data = [];
            foreach ($request->phones as $phone) {
                $data[] = [
                    'to' => $phone['phone'],
                    'text' => $request->message,
                ];
            }

            $response = Http::post(env('WHATSAPP_API_URL')  . "/send-bulk-message", [
                'session' => env('WHATSAPP_API_SESSION'), // Use the session name from your environment variable
                'delay' => $request->delay,
                'data' => $data
            ]);

            if ($response->status() != 200) {
                Alert::error('Error', 'Failed to send bulk message: ' . $response->json()['message'] ?? 'Unknown error');
                return redirect()->back()->with('error', 'Failed to send bulk message: ' . $response->json()['message'] ?? 'Unknown error');
            }

            Alert::success('Success', 'Bulk message has been sent successfully');
            return redirect()->back()->with('success', 'Bulk message has been sent successfully');
        } catch (\Throwable $th) {
            // If there is an error, you can redirect back with an error message
            Alert::error('An error occurred: ' . $th->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $th->getMessage());
        }

        return redirect()->back()->with('success', 'Bulk message sent successfully');
    }
}
