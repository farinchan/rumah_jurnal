<?php

namespace App\Http\Controllers\back;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsappService;
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
    public function chaterySetting()
    {
        $data = [
            'title' => 'Pengaturan Whatsapp - Powered by Chatery',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Pengaturan',
                    'link' => route('back.chatery-whatsapp.setting')
                ]
            ],
        ];

        return view('back.pages.whatsapp_chatery.setting', $data);
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
                    'link' => route('back.chatery-whatsapp.message')
                ]
            ],
        ];

        return view('back.pages.whatsapp_chatery.message.send-message', $data);
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
                    'link' => route('back.chatery-whatsapp.message.sendMessage')
                ],
            ],
            'users' => User::all(),
        ];

        return view('back.pages.whatsapp_chatery.message.send-message', $data);
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
                    'link' => route('back.chatery-whatsapp.message.sendImage')
                ],
            ],
            'users' => User::all(),

        ];

        return view('back.pages.whatsapp_chatery.message.send-image', $data);
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
            $whatsappService = new WhatsappService();
            $whatsappService->sendImage($request->phone, $imagePath, $request->message);

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
                    'link' => route('back.chatery-whatsapp.message.sendBulkMessage')
                ],
            ],

        ];

        return view('back.pages.whatsapp_chatery.message.send-bulk-message', $data);
    }

    public function sendBulkMessageProcess(Request $request)
    {
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
                            $data[] = whatsappNumber($user->phone);
                        }
                    }
                    continue;
                }
                if ($phone == 'user_editors') {
                    $user_editors = User::where("editor_id", '!=', null)->get();
                    foreach ($user_editors as $user) {
                        if ($user->phone) {
                            $data[] = whatsappNumber($user->phone);
                        }
                    }
                    continue;
                }
                if ($phone == 'user_reviewers') {
                    $user_reviewers = User::where("reviewer_id", '!=', null)->get();
                    foreach ($user_reviewers as $user) {
                        if ($user->phone) {
                            $data[] = whatsappNumber($user->phone);
                        }
                    }
                    continue;
                }
                if ($phone == 'user_finances') {
                    $user_finances = User::role('keuangan')->get();
                    foreach ($user_finances as $user) {
                        if ($user->phone) {
                            $data[] = whatsappNumber($user->phone);
                        }
                    }
                    continue;
                }
                if ($phone == 'user_public_relations') {
                    $user_public_relations = User::role('humas')->get();
                    foreach ($user_public_relations as $user) {
                        if ($user->phone) {
                            $data[] = whatsappNumber($user->phone);
                        }
                    }
                    continue;
                }
                if ($phone == 'user_super_admins') {
                    $user_super_admins = User::role('super-admin')->get();
                    foreach ($user_super_admins as $user) {
                        if ($user->phone) {
                            $data[] = whatsappNumber($user->phone);
                        }
                    }
                    continue;
                }
                if ($phone['phone']) {
                    $data[] = whatsappNumber($phone['phone']);
                    continue;
                }
            }

            $whatsappService = new WhatsappService();
            $whatsappService->sendBulkMessage($data, $request->message, $request->delay ?? 1000);

            Alert::success('Success', 'Bulk message has been sent successfully');
            return redirect()->back()->with('success', 'Bulk message has been sent successfully');
        } catch (\Throwable $th) {
            // If there is an error, you can redirect back with an error message
            Alert::error('An error occurred: ' . $th->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $th->getMessage());
        }

        return redirect()->back()->with('success', 'Bulk message sent successfully');
    }

    public function sendMultipleMessageProcess(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'delay' => 'nullable|integer|min:1000',
                'phones' => 'required|array',
                'message' => 'required|string',
                'document' => 'nullable|mimes:pdf,pptx,docx,doc,jpg,png|max:10240',
            ],
            [

                'delay.integer' => 'Delay must be an integer',
                'delay.min' => 'Delay must be at least 1000 milliseconds',
                'phone.required' => 'Phone numbers are required',
                'phone.array' => 'Phone numbers must be an array',
                'phone.*.required' => 'Each phone number is required',
                'message.required' => 'Message is required',
                'document.mimes' => 'File must be a pdf, pptx, docx, doc, jpg, png',
                'document.max' => 'File may not be greater than 10MB',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->first());
        }

        $documentPath = null;
        $mailEnvironment = env('MAIL_ENVIRONMENT', 'local');
        if ($request->hasFile('document')) {
            $document = $request->file('document');
            $fileName = time() . '_' . Auth::id() . '.' . $document->getClientOriginalExtension();
            $path = $document->storeAs('whatsapp_upload', $fileName, 'public');
            $documentPath = url('storage/' . $path);
            if ($mailEnvironment == 'local') {
                $documentPath = "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf";
            }
        }

        try {
            $data = [];
            foreach ($request->phones as $phone) {
                if ($request->hasFile('document')) {
                    $data[] = [
                        'to' => whatsappNumber($phone),
                        'text' => $request->message,
                        'media' => $documentPath,
                        'filename' => $document ? $document->getClientOriginalName() : null,
                    ];
                } else {
                    $data[] = [
                        'to' => whatsappNumber($phone),
                        'text' => $request->message,
                    ];
                }
                continue;
            }

            $response = Http::post(env('WHATSAPP_API_URL')  . "/send-bulk-message", [
                'session' => env('WHATSAPP_API_SESSION'), // Use the session name from your environment variable
                'delay' => $request->delay ?? 1000,
                'data' => $data
            ]);

            if ($response->status() != 200) {
                Alert::error('Error', 'Failed to send multiple message: ' . $response->json()['message'] ?? 'Unknown error');
                return redirect()->back()->with('error', 'Failed to send multiple message: ' . $response->json()['message'] ?? 'Unknown error');
            }

            Alert::success('Success', 'Multiple message has been sent successfully');
            return redirect()->back()->with('success', 'Multiple message has been sent successfully');
        } catch (\Throwable $th) {
            // If there is an error, you can redirect back with an error message
            Alert::error('An error occurred: ' . $th->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $th->getMessage());
        }
    }
}
