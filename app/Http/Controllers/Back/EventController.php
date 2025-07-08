<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class EventController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'List event',
            'menu' => 'event',
            'sub_menu' => 'event',
            'list_event' => Event::latest()->get()
        ];

        return view('back.pages.event.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah event',
            'menu' => 'event',
            'sub_menu' => 'event',
        ];

        return view('back.pages.event.create', $data);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'status' => 'required',
            'name' => 'required',
            'datetime' => 'required',
            'location' => 'nullable',
            'limit' => 'nullable|integer',
            'description' => 'nullable',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:8192',
            'attachment' => 'nullable|mimes:pdf|max:8192',
            'is_active' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
        ], [
            'required' => ':attribute harus diisi',
            'image' => 'File harus berupa gambar',
            'mimes' => 'File harus berupa gambar',
            'max' => 'Ukuran file maksimal 8MB',
            'integer' => ':attribute harus berupa angka',
            'mimes' => 'File harus berupa PDF',
            'max' => 'Ukuran file maksimal 8MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $slug = "";
        if (Event::where('slug', Str::slug($request->name))->count() > 0) {
            $slug = Str::slug($request->name) . '-' . rand(1000, 9999);
        } else {
            $slug = Str::slug($request->name);
        }


        $event = new event();
        $event->type = $request->type;
        $event->status = $request->status;
        $event->name = $request->name;
        $event->slug = $slug;
        $event->datetime = $request->datetime;
        $event->location = $request->location;
        $event->limit = $request->limit;
        $event->description = $request->description;
        $event->meta_title = $request->name;
        $event->meta_description = Str::limit(strip_tags($request->description), 150);
        $event->meta_keywords = implode(", ", array_column(json_decode($request->meta_keywords??"[]"), 'value'));
        $event->is_active = $request->is_active;
        $event->user_id = Auth::user()->id;

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $event->thumbnail =  $image->storeAs('event', date('YmdHis') . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension(), 'public');
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $event->attachment =  $file->storeAs('event', date('YmdHis') . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension(), 'public');
        }

        $event->save();

        Alert::success('Sukses', 'event berhasil ditambahkan');
        return redirect()->route('back.event.index');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit event',
            'menu' => 'event',
            'sub_menu' => 'event',
            'event' => Event::find($id)
        ];

        return view('back.pages.event.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'file' => 'nullable|mimes:pdf|max:8192',
            'title' => 'required',
            'content' => 'required',
            'start' => 'required',
            'end' => 'nullable',
            'meta_keywords' => 'nullable',
            'is_active' => 'required',
        ], [
            'required' => ':attribute harus diisi',
            'image' => 'File harus berupa gambar',
            'mimes' => 'File harus berupa gambar',
            'max' => 'Ukuran file maksimal 2MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $slug = "";
        if (Event::where('slug', Str::slug($request->title))->where('id', '!=', $id)->count() > 0) {
            $slug = Str::slug($request->title) . '-' . rand(1000, 9999);
        } else {
            $slug = Str::slug($request->title);
        }

        $event = Event::find($id);
        $event->title = $request->title;
        $event->slug = $slug;
        $event->content = $request->content;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->meta_title = $request->title;
        $event->meta_description = Str::limit(strip_tags($request->content), 150);
        $event->meta_keywords = implode(", ", array_column(json_decode($request->meta_keywords??"[]"), 'value'));
        $event->is_active = $request->is_active;
        $event->user_id = Auth::user()->id;

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::delete('public/' . $event->image);
            }
            $image = $request->file('image');
            $event->image = $image->storeAs('event', date('YmdHis') . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension(), 'public');
        }

        if ($request->hasFile('file')) {
            if ($event->file) {
                Storage::delete('public/' . $event->file);
            }
            $file = $request->file('file');
            $event->file = $file->storeAs('event', date('YmdHis') . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension(), 'public');
        }

        $event->save();

        Alert::success('Sukses', 'event berhasil di update');
        return redirect()->route('back.event.index');
    }

    public function destroy($id)
    {
        $event = Event::find($id);
        if ($event->file) {
            Storage::delete('public/' . $event->file);
        }
        if ($event->image) {
            Storage::delete('public/' . $event->image);
        }
        $event->delete();

        if ($event->image) {
            Storage::delete('public/' . $event->image);
        }

        Alert::success('Sukses', 'event berhasil dihapus');
        return redirect()->route('back.event.index');
    }

      public function overview($id)
    {
        $data = [
            'title' => 'Overview event',
            'menu' => 'event',
            'sub_menu' => 'event',
            'event' => Event::find($id)
        ];

        return view('back.pages.event.detail.overview', $data);
    }
}
