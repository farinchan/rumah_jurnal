<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'List Pengguna',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Pengguna',
                    'link' => route('back.master.user.index')
                ]
            ],
            'users' => User::all()
        ];

        return view('back.pages.user.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Pengguna',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Pengguna',
                    'link' => route('back.master.user.index')
                ],
                [
                    'name' => 'Tambah Pengguna',
                    'link' => route('back.master.user.create')
                ]
            ]
        ];

        return view('back.pages.user.create', $data);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|image|mimes:jpg,jpeg,png',
            'name' => 'required',
            'gender' => 'nullable|in:laki-laki,perempuan',
            'phone' => 'nullable',
            'username' => 'nullable|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'sinta_id' => 'nullable',
            'scopus_id' => 'nullable',
            'google_scholar' => 'nullable',
            'password' => 'required',
        ], [
            'photo.image' => 'Foto harus berupa gambar',
            'photo.mimes' => 'Foto harus berformat jpg, jpeg, png',
            'name.required' => 'Nama harus diisi',
            'gender.in' => 'Jenis kelamin harus laki-laki atau perempuan',
            'phone.required' => 'Nomor telepon harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'sinta_id.required' => 'ID Sinta harus diisi',
            'scopus_id.required' => 'ID Scopus harus diisi',
            'google_scholar.required' => 'Google Scholar harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->all());
        }

        $user = new User();
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->sinta_id = $request->sinta_id;
        $user->scopus_id = $request->scopus_id;
        $user->google_scholar = $request->google_scholar;
        $user->password = bcrypt($request->password);
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = Str::slug($request->name) . "-" . time() . "." . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('uploads/user/photo', $photoName, 'public');
            $user->photo = $photoPath;
        }
        $user->save();

        if ($request->role_admin) {
            $user->assignRole('super-admin');
        }
        if ($request->role_keuangan) {
            $user->assignRole('keuangan');
        }
        if ($request->role_editor) {
            $user->assignRole('editor');
        }
        if ($request->role_humas) {
            $user->assignRole('humas');
        }

        if ($request->permissions) {
            foreach ($request->permissions as $permission) {
                $user->givePermissionTo($permission);
            }
        }

        Alert::success('Berhasil', 'Data pengguna berhasil ditambahkan');
        return redirect()->route('back.master.user.index')->with('success', 'Data pengguna berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Pengguna',
            'menu' => 'Pengguna',
            'sub_menu' => '',
            'user' => User::find($id)
        ];

        return view('back.pages.user.edit', $data);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|image|mimes:jpg,jpeg,png',
            'name' => 'required',
            'gender' => 'nullable|in:laki-laki,perempuan',
            'phone' => 'nullable',
            'username' => 'nullable|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'sinta_id' => 'nullable',
            'scopus_id' => 'nullable',
            'google_scholar' => 'nullable',
            'password' => 'nullable',
        ], [
            'photo.image' => 'Foto harus berupa gambar',
            'photo.mimes' => 'Foto harus berformat jpg, jpeg, png',
            'name.required' => 'Nama harus diisi',
            'gender.in' => 'Jenis kelamin harus laki-laki atau perempuan',
            'phone.required' => 'Nomor telepon harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'sinta_id.required' => 'ID Sinta harus diisi',
            'scopus_id.required' => 'ID Scopus harus diisi',
            'google_scholar.required' => 'Google Scholar harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->all());
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->sinta_id = $request->sinta_id;
        $user->scopus_id = $request->scopus_id;
        $user->google_scholar = $request->google_scholar;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = Str::slug($request->name) . "-" . time() . "." . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('uploads/user/photo', $photoName, 'public');
            $user->photo = $photoPath;
        }
        $user->save();


        if ($request->role_admin) {
            $user->assignRole('super-admin');
        } else {
            $user->removeRole('super-admin');
        }
        if ($request->role_keuangan) {
            $user->assignRole('keuangan');
        } else {
            $user->removeRole('keuangan');
        }
        if ($request->role_editor) {
            $user->assignRole('editor');
        } else {
            $user->removeRole('editor');
        }
        if ($request->role_humas) {
            $user->assignRole('humas');
        } else {
            $user->removeRole('humas');
        }

        $user->permissions()->detach();
        if ($request->permissions) {
            foreach ($request->permissions as $permission) {
                $user->givePermissionTo($permission);
            }
        }

        Alert::success('Berhasil', 'Data pengguna berhasil diubah');
        return redirect()->route('back.master.user.index')->with('success', 'Data pengguna berhasil diubah');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user?->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->delete();

        return redirect()->route('back.master.user.index')->with('success', 'Data pengguna berhasil dihapus');
    }
}
