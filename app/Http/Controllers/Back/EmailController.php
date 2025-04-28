<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'required',
            'body' => 'required',
            'attachment' => 'nullable|mimes:pdf,pptx,docx,doc|max:10240',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'subject.required' => 'Subject harus diisi',
            'body.required' => 'body harus diisi',
            'attachment.mimes' => 'File harus berupa pdf, pptx, docx, doc',
            'attachment.max' => 'File tidak boleh lebih dari 10 MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            EmailLog::create([
                'to' => $request->email,
                'subject' => $request->subject,
                'body' => $request->body,   
                'status' => 'sent',
            ]);

            Mail::send([], [], function ($message) use ($request) {
                $message->to($request->email)
                ->subject($request->subject)
                ->html($request->body); // Set the body as HTML content

                if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('mail-attachments', $filename, 'public');
                $message->attach(storage_path('app/public/mail-attachments/' .  $filename ), [
                    'as' => $filename,
                    'mime' => $file->getClientMimeType(),
                ]);
                }
            });
        } catch (\Throwable $th) {
            // Alert::error('Error', 'Gagal mengirim email: ' . $th->getMessage());
            // return redirect()->back()->withInput();
        }


        Alert::success('Success', 'Email has been sent');
        return redirect()->back();
    }
}
