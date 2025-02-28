<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function category()
    {
        $data = [
            'title' => 'Kategori Berita',
            'breadcrumbs' => [
                [
                    'name' => 'Kategori Berita',
                    'link' => route('back.news.category')
                ]
            ],
            'categories' => NewsCategory::all()
        ];

        return view('back.pages.news.category', $data);
    }

    public function categoryStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:news_categories,name',
            'description' => 'nullable',
        ], [
            'name.required' => 'Nama kategori harus diisi',
            'name.unique' => 'Nama kategori sudah ada'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->all());
        }

        NewsCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'meta_title' => $request->name,
            'meta_description' => $request->description,
            'meta_keywords' => implode(", ", explode(" ", $request->name)),
        ]);

        return redirect()->route('back.news.category')->with('success', 'Kategori Berita berhasil ditambahkan');
    }

    public function categoryUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:news_categories,name,' . $id,
            'description' => 'nullable',
        ], [
            'name.required' => 'Nama kategori harus diisi',
            'name.unique' => 'Nama kategori sudah ada'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->all());
        }

        $category = NewsCategory::find($id);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'meta_title' => $request->name,
            'meta_description' => $request->description,
            'meta_keywords' => implode(", ", explode(" ", $request->name)),
        ]);

        return redirect()->route('back.news.category')->with('success', 'Kategori Berita berhasil diubah');
    }

    public function categoryDestroy($id)
    {
        $category = NewsCategory::find($id);
        $category->delete();

        return redirect()->route('back.news.category')->with('success', 'Kategori Berita berhasil dihapus');
    }

    public function index()
    {
        $data = [
            'title' => 'Berita',
            'breadcrumbs' => [
                [
                    'name' => 'Berita',
                    'link' => route('back.news.index')
                ]
            ],
            'list_news' => News::all()
        ];

        return view('back.pages.news.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Berita',
            'breadcrumbs' => [
                [
                    'name' => 'Berita',
                    'link' => route('back.news.index')
                ],
                [
                    'name' => 'Tambah Berita',
                    'link' => route('back.news.create')
                ]
            ],
            'categories' => NewsCategory::all(),
            'news' => News::all()
        ];

        return view('back.pages.news.create', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'status' => 'required',
                'meta_keywords' => 'nullable',
            ],
            [
                'image' => 'File harus berupa gambar',
                'mimes' => 'Format file harus :values',
                'max' => 'Ukuran file maksimal :max KB',
                'required' => 'Kolom :attribute harus diisi'
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->all());
        }

        $slug = "";
        if (News::where('slug', Str::slug($request->title))->count() > 0) {
            $slug = Str::slug($request->title) . '-' . rand(1000, 9999);
        } else {
            $slug = Str::slug($request->title);
        }

        $news = new News();
        $news->title = $request->title;
        $news->slug = $slug;
        $news->content = $request->content;
        $news->news_category_id = $request->category_id;
        $news->user_id = Auth::user()->id;
        $news->status = $request->status;
        $news->meta_title = $request->title;
        $news->meta_description = Str::limit(strip_tags($request->content), 150);
        $news->meta_keywords = implode(", ", array_column(json_decode($request->meta_keywords), 'value'));

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $news->thumbnail = $thumbnail->storeAs('news', date('YmdHis') . '_' . Str::slug($request->title) . '.' . $thumbnail->getClientOriginalExtension(), 'public');
        }

        $news->save();

        return redirect()->route('back.news.index')->with('success', 'Berita berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Berita',
            'breadcrumbs' => [
                [
                    'name' => 'Berita',
                    'link' => route('back.news.index')
                ],
                [
                    'name' => 'Edit Berita',
                    'link' => route('back.news.edit', $id)
                ]
            ],
            'categories' => NewsCategory::all(),
            'news' => News::find($id)
        ];

        return view('back.pages.news.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'status' => 'required',
                'meta_keywords' => 'nullable',
            ],
            [
                'image' => 'File harus berupa gambar',
                'mimes' => 'Format file harus :values',
                'max' => 'Ukuran file maksimal :max KB',
                'required' => 'Kolom :attribute harus diisi'
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', $validator->errors()->all());
        }

        $slug = "";
        if (News::where('slug', Str::slug($request->title))->where('id', '!=', $id)->count() > 0) {
            $slug = Str::slug($request->title) . '-' . rand(1000, 9999);
        } else {
            $slug = Str::slug($request->title);
        }

        $news = News::find($id);
        $news->title = $request->title;
        $news->slug = $slug;
        $news->content = $request->content;
        $news->news_category_id = $request->category_id;
        $news->user_id = Auth::user()->id;
        $news->status = $request->status;
        $news->meta_title = $request->title;
        $news->meta_description = Str::limit(strip_tags($request->content), 150);
        $news->meta_keywords = implode(", ", array_column(json_decode($request->meta_keywords), 'value'));;

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $news->thumbnail = $thumbnail->storeAs('news', date('YmdHis') . '_' . Str::slug($request->title) . '.' . $thumbnail->getClientOriginalExtension(), 'public');
        }

        $news->save();

        return redirect()->route('back.news.index')->with('success', 'Berita berhasil diubah');
    }

    public function destroy($id)
    {
        $news = News::find($id);
        $news->delete();

        return redirect()->back()->with('success', 'Berita berhasil dihapus');
    }

    public function comment()
    {
        $data = [
            'title' => 'Komentar Berita',
            'breadcrumbs' => [
                [
                    'name' => 'Komentar Berita',
                    'link' => route('back.news.comment')
                ]
            ],
            'comments' => NewsComment::with('news')->get()
        ];

        return view('back.pages.news.comment', $data);
    }

    public function commentSpam($id)
    {
        $comment = NewsComment::find($id);
        $comment->status = 'spam';
        $comment->save();

        return redirect()->back()->with('success', 'Komentar berhasil ditandai sebagai spam');
    }
}
