<?php

namespace App\Http\Controllers\back;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'users' => User::all(),
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
            'users' => User::all(),

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
            Alert::error('Validation Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->first());
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '_' . Auth::id() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('upload', $fileName, 'public');
            $imagePath = asset('storage/' . $path);
        }

        $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
        if ($mailEnvirontment == 'local') {

            $imagePath = "https://upload.wikimedia.org/wikipedia/id/b/b0/Kamen_rider_eurodata.png";
        }

        try {
            $response = Http::post(env('WHATSAPP_API_URL')  . "/send-image", [
                'session' => env('WHATSAPP_API_SESSION'), // Use the session name from your environment variable
                'to' => whatsappNumber($request->phone),
                'image' => $imagePath,
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





        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->first());
        }

        try {
            $data = [];
            foreach ($request->phones as $phone) {
                if ($phone == 'users') {
                    $users = User::all();
                    foreach ($users as $user) {
                        if ($user->phone) {
                            $data[] = [
                                'to' => whatsappNumber($user->phone) ,
                                'text' => $request->message,
                            ];
                        }
                    }
                    continue;
                }
                if ($phone == 'user_editors') {
                    $user_editors = User::role('editor')->get();
                    foreach ($user_editors as $user) {
                        if ($user->phone) {
                            $data[] = [
                                'to' => whatsappNumber($user->phone),
                                'text' => $request->message,
                            ];
                        }
                    }
                    continue;
                }
                if ($phone == 'user_finances') {
                    $user_finances = User::role('keuangan')->get();
                    foreach ($user_finances as $user) {
                        if ($user->phone) {
                            $data[] = [
                                'to' => whatsappNumber($user->phone),
                                'text' => $request->message,
                            ];
                        }
                    }
                    continue;
                }
                if ($phone == 'user_public_relations') {
                    $user_public_relations = User::role('humas')->get();
                    foreach ($user_public_relations as $user) {
                        if ($user->phone) {
                            $data[] = [
                                'to' => whatsappNumber($user->phone),
                                'text' => $request->message,
                            ];
                        }
                    }
                    continue;
                }
                if ($phone == 'user_super_admins') {
                    $user_super_admins = User::role('super-admin')->get();
                    foreach ($user_super_admins as $user) {
                        if ($user->phone) {
                            $data[] = [
                                'to' => whatsappNumber($user->phone),
                                'text' => $request->message,
                            ];
                        }
                    }
                    continue;
                }
                if ($phone['phone']) {
                    $data[] = [
                        'to' => whatsappNumber($phone['phone']),
                        'text' => $request->message,
                    ];
                    continue;
                }
            }

            // dd($data);

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
