<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\WelcomeSpeech;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'image.required' => 'Gambar wajib diisi',
            'image.image' => 'Gambar harus berupa gambar',
            'image.mimes' => 'Gambar harus berformat jpeg, png, jpg, gif, svg',
            'image.max' => 'Ukuran gambar maksimal 2MB',
            'content.required' => 'Konten wajib diisi',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sekapur_sirih = WelcomeSpeech::first();



        if (!$sekapur_sirih) {
            $sekapur_sirih = new WelcomeSpeech();
        }

        if ($request->hasFile('image')) {
            $sekapur_sirih->fill([
                'name' => $request->name,
                'image' => $request->file('image')->storeAs('sekapur_sirih', 'sekapur_sirih.' . $request->file('image')->extension(), 'public'),
                'content' => $request->content,
            ]);
        } else {
            $sekapur_sirih->fill([
                'name' => $request->name,
                'content' => $request->content,
            ]);
        }

        $sekapur_sirih->save();

        Alert::success('Berhasil', 'Data berhasil diupdate');
        return redirect()->route('back.welcomeSpeech.index');
    }
}
