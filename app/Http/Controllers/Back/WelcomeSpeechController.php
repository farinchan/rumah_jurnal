<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\WelcomeSpeech;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class WelcomeSpeechController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Kata Sambutan',
            'breadcrumb' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Kata Sambutan',
                    'link' => route('back.welcomeSpeech.index')
                ],
            ],
            'data' => WelcomeSpeech::first() ?? new WelcomeSpeech(),
        ];

        return view('back.pages.welcome_speech', $data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'title' => 'required|max:255',
            'subtitle' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'title.required' => 'Judul Utama wajib diisi',
            'title.max' => 'Judul Utama maksimal 255 karakter',
            'subtitle.max' => 'Subjudul maksimal 255 karakter',
            'image.image' => 'Gambar harus berupa gambar',
            'image.mimes' => 'Gambar harus berformat jpeg, png, jpg, gif, svg',
            'image.max' => 'Ukuran gambar maksimal 2MB',
            'content.required' => 'Konten wajib diisi',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'content' => $request->content,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::random(20) . '.' . $file->extension();
            $data['image'] = $file->storeAs('sekapur_sirih', $filename, 'public');
        }

        WelcomeSpeech::updateOrCreate(
            ['id' => WelcomeSpeech::first()?->id],
            $data
        );

        Alert::success('Berhasil', 'Data berhasil diupdate');
        return redirect()->route('back.welcomeSpeech.index');
    }
}
