<?php

namespace App\Http\Controllers\Back;

use App\Exports\EventAttendanceUserExport;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventAttendanceUser;
use App\Models\EventUser;
use App\Models\Reviewer;
use App\Models\Editor;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

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
            'material' => 'nullable|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar|max:102400',
        ], [
            'type.required' => 'Tipe event harus diisi',
            'status.required' => 'Status event harus diisi',
            'name.required' => 'Nama event harus diisi',
            'datetime.required' => 'Tanggal dan waktu event harus diisi',
            'location.required' => 'Lokasi event harus diisi',
            'limit.integer' => 'Limit peserta harus berupa angka',
            'description.required' => 'Deskripsi event harus diisi',
            'thumbnail.image' => 'File thumbnail harus berupa gambar',
            'thumbnail.mimes' => 'File thumbnail harus berupa: jpeg, png, jpg, gif, svg',
            'thumbnail.max' => 'Ukuran file thumbnail maksimal 8MB',
            'attachment.mimes' => 'File lampiran harus berupa: pdf',
            'attachment.max' => 'Ukuran file lampiran maksimal 8MB',
            'is_active.required' => 'Status aktif event harus diisi',
            'meta_title.required' => 'Meta title harus diisi',
            'meta_description.required' => 'Meta description harus diisi',
            'meta_keywords.required' => 'Meta keywords harus diisi',
            'material.mimes' => 'File materi harus berupa: pdf, doc, docx, ppt, pptx, xls, xlsx, txt, zip, rar',
            'material.max' => 'Ukuran file materi maksimal 100MB',
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
        $event->meta_keywords = implode(", ", array_column(json_decode($request->meta_keywords ?? "[]"), 'value'));
        $event->is_active = $request->is_active;
        $event->user_id = Auth::user()->id;

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $event->thumbnail =  $image->storeAs('event', date('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension(), 'public');
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $event->attachment =  $file->storeAs('event', date('YmdHis') . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension(), 'public');
        }

        if ($request->hasFile('material')) {
            $file = $request->file('material');
            $event->material =  $file->storeAs('event', 'm-' . date('YmdHis') . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension(), 'public');
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
            'material' => 'nullable|mimes:pdf,doc,docx,ppt,pptx,xls,txt,xlsx,zip,rar|max:102400',
        ], [
            'type.required' => 'Tipe event harus diisi',
            'status.required' => 'Status event harus diisi',
            'name.required' => 'Nama event harus diisi',
            'datetime.required' => 'Tanggal dan waktu event harus diisi',
            'location.required' => 'Lokasi event harus diisi',
            'limit.integer' => 'Limit peserta harus berupa angka',
            'description.required' => 'Deskripsi event harus diisi',
            'thumbnail.image' => 'File thumbnail harus berupa gambar',
            'thumbnail.mimes' => 'File thumbnail harus berupa: jpeg, png, jpg, gif, svg',
            'thumbnail.max' => 'Ukuran file thumbnail maksimal 8MB',
            'attachment.mimes' => 'File lampiran harus berupa: pdf',
            'attachment.max' => 'Ukuran file lampiran maksimal 8MB',
            'is_active.required' => 'Status aktif event harus diisi',
            'meta_title.required' => 'Meta title harus diisi',
            'meta_description.required' => 'Meta description harus diisi',
            'meta_keywords.required' => 'Meta keywords harus diisi',
            'material.mimes' => 'File materi harus berupa: pdf, doc, docx, ppt, pptx, xls, xlsx, txt, zip, rar',
            'material.max' => 'Ukuran file materi maksimal 100MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $event = Event::find($id);
        $event->type = $request->type;
        $event->status = $request->status;
        $event->name = $request->name;
        $event->datetime = $request->datetime;
        $event->location = $request->location;
        $event->limit = $request->limit;
        $event->description = $request->description;
        $event->meta_title = $request->name;
        $event->meta_description = Str::limit(strip_tags($request->description), 150);
        $event->meta_keywords = implode(", ", array_column(json_decode($request->meta_keywords ?? "[]"), 'value'));
        $event->is_active = $request->is_active;
        $event->user_id = Auth::user()->id;


        if ($request->hasFile('thumbnail')) {
            if ($event->thumbnail) {
                Storage::delete('public/' . $event->thumbnail);
            }
            $image = $request->file('thumbnail');
            $event->thumbnail = $image->storeAs('event', date('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension(), 'public');
        }

        if ($request->hasFile('attachment')) {
            if ($event->attachment) {
                Storage::delete('public/' . $event->attachment);
            }
            $file = $request->file('attachment');
            $event->attachment = $file->storeAs('event', date('YmdHis') . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension(), 'public');
        }

        if ($request->hasFile('material')) {
            if ($event->material) {
                Storage::delete('public/' . $event->material);
            }
            $file = $request->file('material');
            $event->material = $file->storeAs('event', 'm-' . date('YmdHis') . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension(), 'public');
        }

        $event->save();

        Alert::success('Sukses', 'event berhasil di update');
        return redirect()->route('back.event.detail.overview', $id);
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

    public function participant($id)
    {
        $data = [
            'title' => 'List peserta event',
            'menu' => 'event',
            'sub_menu' => 'event',
            'event' => Event::find($id),
            'users' => EventUser::where('event_id', $id)->with(['user'])->latest()->get()
        ];

        return view('back.pages.event.detail.participant', $data);
    }

    public function participantStore(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'nullable',
        ], [
            'required' => ':attribute harus diisi',
            'email' => ':attribute harus berupa email yang valid',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            // Check if user already exists as participant
            $existingParticipant = EventUser::where('event_id', $id)
                ->where('email', $request->email)
                ->first();

            if ($existingParticipant) {
                Alert::error('Error', 'Peserta dengan email ' . $request->email . ' sudah terdaftar');
                return redirect()->back()->withInput();
            }

            $eventUser = new EventUser();
            $eventUser->event_id = $id;
            $eventUser->user_id = $user->id;
            $eventUser->name = $request->name;
            $eventUser->email = $request->email;
            $eventUser->phone = $request->phone;
            $eventUser->save();

            if ($eventUser->phone && $eventUser->phone != '-') {
                try {
                    $response_wa = Http::timeout(60)->post(env('WHATSAPP_API_URL')  . "/send-message", [
                        'session' => env('WHATSAPP_API_SESSION'),
                        'to' => whatsappNumber($eventUser->phone),
                        'text' => "Halo Bapak/Ibu " . $eventUser->name . ",\n\n" .
                            "Selamat! Anda telah ditambahkan ke event *" . ($eventUser->event->type ?? '') . "* " . ($eventUser->event->status ?? '') .  " sebagai peserta.\n\n" .
                            "Berikut detail acara:\n" .
                            "• Nama Event: " . ($eventUser->event->name ?? '-') . "\n" .
                            "• Tanggal & Waktu: " . ($eventUser->event->datetime ?? '-') . "\n" .
                            "• " . ($eventUser->event->status == 'online' ? 'Link' : 'Lokasi') . ": " . ($eventUser->event->location ?? '-') . "\n\n" .
                            "Pastikan Anda hadir dan catat jadwalnya!\n" .
                            "Terima kasih telah bergabung.\n\n" .
                            "_generate by system\n" .
                            url('/'),
                    ]);

                    if ($response_wa->status() != 200) {
                        Log::error('Failed to send WhatsApp messages: ' . $response_wa->body());
                    }
                } catch (\Throwable $th) {
                    Log::error('Error in sending WhatsApp message: ' . $th->getMessage());
                }
            }

            Alert::success('Sukses', 'Peserta berhasil ditambahkan, user sudah terdaftar sebelumnya');
            return redirect()->back();

        } else {
            // Create new user
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = bcrypt('rumahjurnal123'); // Set a default password or generate a random one
            $user->save();

            $eventUser = new EventUser();
            $eventUser->event_id = $id;
            $eventUser->user_id = $user->id;
            $eventUser->name = $request->name;
            $eventUser->email = $request->email;
            $eventUser->phone = $request->phone;
            $eventUser->save();

            if ($eventUser->phone && $eventUser->phone != '-') {
                try {
                    $response_wa = Http::timeout(60)->post(env('WHATSAPP_API_URL')  . "/send-message", [
                        'session' => env('WHATSAPP_API_SESSION'),
                        'to' => whatsappNumber($eventUser->phone),
                        'text' => "Halo Bapak/Ibu " . $eventUser->name . ",\n\n" .
                            "Selamat! Anda telah ditambahkan ke event *" . ($eventUser->event->type ?? '') . "* " . ($eventUser->event->status ?? '') .  " sebagai peserta.\n\n" .
                            "Berikut detail acara:\n" .
                            "• Nama Event: " . ($eventUser->event->name ?? '-') . "\n" .
                            "• Tanggal & Waktu: " . ($eventUser->event->datetime ?? '-') . "\n" .
                            "• " . ($eventUser->event->status == 'online' ? 'Link' : 'Lokasi') . ": " . ($eventUser->event->location ?? '-') . "\n\n" .
                            "Pastikan Anda hadir dan catat jadwalnya!\n" .
                            "Terima kasih telah bergabung.\n\n" .
                            "_generate by system\n" .
                            url('/'),
                    ]);

                    if ($response_wa->status() != 200) {
                        Log::error('Failed to send WhatsApp messages: ' . $response_wa->body());
                    }
                } catch (\Throwable $th) {
                    Log::error('Error in sending WhatsApp message: ' . $th->getMessage());
                }
            }

            Alert::success('Sukses', 'Peserta berhasil ditambahkan, user baru telah dibuat');
            return redirect()->back();
        }

        Alert::warning('Warning', 'Terjadi Kesalahan');
        return redirect()->back();
    }

    public function participantDestroy($id, $eventUserId)
    {
        $eventUser = EventUser::find($eventUserId);
        if ($eventUser) {
            $eventUser->delete();
            Alert::success('Sukses', 'Peserta berhasil dihapus');
        } else {
            Alert::error('Error', 'Peserta tidak ditemukan');
        }

        return redirect()->back();
    }

    public function participantImportReviewerModal($id)
    {
        try {
            // Log untuk debugging
            Log::info('participantImportReviewerModal called with id: ' . $id);

            // Get unique reviewers - simple version first
            $reviewers = Reviewer::with(["user"])
                ->select('id', 'reviewer_id', 'name', 'email', 'phone', 'affiliation')
                ->orderByDesc('id')
                ->get()
                ->unique('reviewer_id')
                ->values();

            Log::info('Found reviewers count: ' . $reviewers->count());

            $result = [
                'reviewers' => $reviewers->map(function ($reviewer) {
                    return [
                        'id' => $reviewer->id,
                        'reviewer_id' => $reviewer->reviewer_id,
                        'name' => $reviewer->name ?? 'Unknown Reviewer',
                        'email' => $reviewer->user->email ?? $reviewer->email ?? 'No Email',
                        'phone' => $reviewer->user->phone ?? $reviewer->phone ?? '-',
                        'affiliation' => $reviewer->affiliation ?? '-',
                        'journals' =>  $reviewer->journal = Reviewer::where('reviewer_id', $reviewer->reviewer_id)->with('issue.journal')
                            ->get()
                            ->map(function ($item) {
                                $journal_data = $item->issue->journal;
                                return (object) [
                                    'id' => $journal_data->id,
                                    'name' => $journal_data->name,
                                    'title' => $journal_data->title,
                                    'url_path' => $journal_data->url_path,
                                ];
                            })->unique('id')->values(),
                    ];
                })
            ];

            Log::info('Returning reviewer data: ', $result);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error in participantImportReviewerModal: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load reviewers: ' . $e->getMessage(),
                'reviewers' => []
            ]);
        }
    }

    public function participantImportReviewer(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reviewer_ids' => 'required|array',
            'reviewer_ids.*' => 'exists:reviewers,id'
        ], [
            'required' => 'Pilih minimal satu reviewer',
            'array' => 'Data reviewer tidak valid',
            'exists' => 'Reviewer tidak ditemukan'
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $importedCount = 0;
        $skippedCount = 0;

        $data_wa = [];
        foreach ($request->reviewer_ids as $reviewerId) {
            $reviewer = Reviewer::with(['user'])->orderByDesc('id')->where('id', $reviewerId)->first();

            if (!$reviewer) {
                continue;
            }

            // Check if reviewer already exists as participant
            $existingParticipant = EventUser::where('event_id', $id)
                ->where('email', $reviewer->email)
                ->first();

            if ($existingParticipant) {
                $skippedCount++;
                continue;
            }

            // Create new event participant from reviewer
            $eventUser = new EventUser();
            $eventUser->event_id = $id;
            $eventUser->user_id = User::where('reviewer_id', $reviewer->reviewer_id)->value('id') ?? null;
            $eventUser->name = $reviewer->name;
            $eventUser->email = $reviewer->user->email ?? $reviewer->email;
            $eventUser->phone = $reviewer->user->phone ?? $reviewer->phone;
            $eventUser->save();

            $importedCount++;

            if ($eventUser->phone && $eventUser->phone != '-') {
                $data_wa[] = [
                    'to' => env('MAIL_ENVIRONMENT') == 'production' ? whatsappNumber($eventUser->phone) : whatsappNumber(env('WHATSAPP_ADMIN_NUMBER')),
                    'text' => "Halo Bapak/Ibu " . $eventUser->name . ",\n\n" .
                        "Selamat! Anda telah ditambahkan ke event *" . ($eventUser->event->type ?? '') . "* " . ($eventUser->event->status ?? '') .  " sebagai peserta.\n\n" .
                        "Berikut detail acara:\n" .
                        "• Nama Event: " . ($eventUser->event->name ?? '-') . "\n" .
                        "• Tanggal & Waktu: " . ($eventUser->event->datetime ?? '-') . "\n" .
                        "• " . ($eventUser->event->status == 'online' ? 'Link' : 'Lokasi') . ": " . ($eventUser->event->location ?? '-') . "\n\n" .
                        "Pastikan Anda hadir dan catat jadwalnya!\n" .
                        "Terima kasih telah bergabung.\n\n" .
                        "_generate by system_\n" .
                        url('/'),
                ];
            }
        }

        $response = Http::timeout(60)->post(env('WHATSAPP_API_URL')  . "/send-bulk-message", [
            'session' => env('WHATSAPP_API_SESSION'), // Use the session name from your environment variable
            'delay' => 2000,
            'data' => $data_wa
        ]);
        if ($response->status() != 200) {
            Log::error('Failed to send WhatsApp messages: ' . $response->body());
        }

        $message = "Import selesai. {$importedCount} reviewer berhasil diimport";
        if ($skippedCount > 0) {
            $message .= ", {$skippedCount} reviewer dilewati (sudah terdaftar)";
        }

        Alert::success('Sukses', $message);
        return redirect()->back();
    }

    public function participantImportEditorModal($id)
    {
        try {
            // Log untuk debugging
            Log::info('participantImportEditorModal called with id: ' . $id);

            // Get unique editors - simple version first
            $editors = Editor::with('user')
                ->select('id', 'editor_id', 'name', 'email', 'phone', 'affiliation')
                ->orderByDesc('id')
                ->get()
                ->unique('editor_id')
                ->values();

            Log::info('Found editors count: ' . $editors->count());

            $result = [
                'editors' => $editors->map(function ($editor) {
                    return [
                        'id' => $editor->id,
                        'editor_id' => $editor->editor_id,
                        'name' => $editor->name ?? 'Unknown Editor',
                        'email' => $editor->user->email ?? $editor->email ?? 'No Email',
                        'phone' => $editor->user->phone ?? $editor->phone ?? '-',
                        'affiliation' => $editor->affiliation ?? '-',
                        'journals' => $editor->journal = Editor::where('editor_id', $editor->editor_id)->with('issue.journal')
                            ->get()
                            ->map(function ($item) {
                                $journal_data = $item->issue->journal;
                                return (object) [
                                    'id' => $journal_data->id,
                                    'name' => $journal_data->name,
                                    'title' => $journal_data->title,
                                    'url_path' => $journal_data->url_path,
                                ];
                            })->unique('id')->values(),
                    ];
                })
            ];

            Log::info('Returning data: ', $result);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error in participantImportEditorModal: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load editors: ' . $e->getMessage(),
                'editors' => []
            ]);
        }
    }

    public function participantImportEditor(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'editor_ids' => 'required|array',
            'editor_ids.*' => 'exists:editors,id'
        ], [
            'required' => 'Pilih minimal satu editor',
            'array' => 'Data editor tidak valid',
            'exists' => 'Editor tidak ditemukan'
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $importedCount = 0;
        $skippedCount = 0;

        $data_wa = [];
        foreach ($request->editor_ids as $editorId) {
            $editor = Editor::with('user')->orderByDesc('id')->where('id', $editorId)->first();

            if (!$editor) {
                continue;
            }

            // Check if editor already exists as participant
            $existingParticipant = EventUser::where('event_id', $id)
                ->where('email', $editor->email)
                ->first();

            if ($existingParticipant) {
                $skippedCount++;
                continue;
            }

            // Create new event participant from editor
            $eventUser = new EventUser();
            $eventUser->user_id = User::where('editor_id', $editor->editor_id)->value('id') ?? null;
            $eventUser->event_id = $id;
            $eventUser->name = $editor->name;
            $eventUser->email = $editor->user->email ?? $editor->email ?? 'No Email';
            $eventUser->phone = $editor->user->phone ?? $editor->phone ?? '-';
            $eventUser->save();

            $importedCount++;

            if ($eventUser->phone && $eventUser->phone != '-') {
                $data_wa[] = [
                    'to' => env('MAIL_ENVIRONMENT') == 'production' ? whatsappNumber($eventUser->phone) : whatsappNumber(env('WHATSAPP_ADMIN_NUMBER')),
                    'text' => "Halo Bapak/Ibu " . $eventUser->name . ",\n\n" .
                        "Selamat! Anda telah ditambahkan ke event *" . ($eventUser->event->type ?? '') . "* " . ($eventUser->event->status ?? '') .  " sebagai peserta.\n\n" .
                        "Berikut detail acara:\n" .
                        "• Nama Event: " . ($eventUser->event->name ?? '-') . "\n" .
                        "• Tanggal & Waktu: " . ($eventUser->event->datetime ?? '-') . "\n" .
                        "• " . ($eventUser->event->status == 'online' ? 'Link' : 'Lokasi') . ": " . ($eventUser->event->location ?? '-') . "\n\n" .
                        "Pastikan Anda hadir dan catat jadwalnya!\n" .
                        "Terima kasih telah bergabung.\n\n" .
                        "_generate by system_\n" .
                        url('/'),
                ];
            }
        }

        try {
            $response = Http::timeout(60)->post(env('WHATSAPP_API_URL')  . "/send-bulk-message", [
                'session' => env('WHATSAPP_API_SESSION'), // Use the session name from your environment variable
                'delay' => 2000,
                'data' => $data_wa
            ]);

            if ($response->status() != 200) {
                Log::error('Failed to send WhatsApp messages: ' . $response->body());
            }
        } catch (\Throwable $th) {
            Log::error('Error in sending WhatsApp messages: ' . $th->getMessage());
            Alert::error('Error', 'Gagal mengirim pesan WhatsApp: ' . $th->getMessage());
        }



        $message = "Import selesai. {$importedCount} editor berhasil diimport";
        if ($skippedCount > 0) {
            $message .= ", {$skippedCount} editor dilewati (sudah terdaftar)";
        }

        Alert::success('Sukses', $message);
        return redirect()->back();
    }

    public function attendance(Request $request, $id)
    {
        $eventAttendances = EventAttendance::where('event_id', $id)->get();

        $data = [
            'title' => 'List kehadiran peserta event',
            'menu' => 'event',
            'sub_menu' => 'event',
            'event' => Event::find($id),
            'attendances' => $eventAttendances
        ];
        return view('back.pages.event.detail.attendance', $data);
    }

    public function attendanceStore(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after_or_equal:start_datetime',
        ], [
            'required' => ':attribute harus diisi',
            'date' => ':attribute harus berupa tanggal yang valid',
            'after_or_equal' => ':attribute harus setelah atau sama dengan :other',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $attendance = new EventAttendance();
        $attendance->code = strtoupper(Str::random(10)) . '-' . $id;
        $attendance->event_id = $id;
        $attendance->name = $request->name;
        $attendance->description = $request->description;
        $attendance->start_datetime = $request->start_datetime;
        $attendance->end_datetime = $request->end_datetime;
        $attendance->save();

        Alert::success('Sukses', 'Daftar kehadiran berhasil ditambahkan');
        return redirect()->back();
    }

    public function attendanceDestroy($id, $attendanceId)
    {
        $attendance = EventAttendance::find($attendanceId);
        if ($attendance) {
            $attendance->delete();
            Alert::success('Sukses', 'Daftar kehadiran berhasil dihapus');
        } else {
            Alert::error('Error', 'Daftar kehadiran tidak ditemukan');
        }

        return redirect()->back();
    }

    public function attendanceUpdate(Request $request, $id, $attendanceId)
    {
        $attendance = EventAttendance::find($attendanceId);
        if (!$attendance) {
            Alert::error('Error', 'Daftar kehadiran tidak ditemukan');
            return redirect()->route('back.event.attendance', ['id' => $id]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after_or_equal:start_datetime',
        ], [
            'required' => ':attribute harus diisi',
            'date' => ':attribute harus berupa tanggal yang valid',
            'after_or_equal' => ':attribute harus setelah atau sama dengan :other',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $attendance->name = $request->name;
        $attendance->description = $request->description;
        $attendance->start_datetime = $request->start_datetime;
        $attendance->end_datetime = $request->end_datetime;
        $attendance->save();

        Alert::success('Sukses', 'Daftar kehadiran berhasil diperbarui');
        return redirect()->back();
    }

    public function attendanceDetail($id, $attendanceId)
    {
        $attendance = EventAttendance::with(['attendances'])->find($attendanceId);
        if (!$attendance) {
            Alert::error('Error', 'Daftar kehadiran tidak ditemukan');
            return redirect()->route('back.event.attendance', ['id' => $id]);
        }

        $data = [
            'title' => 'Detail kehadiran peserta event',
            'menu' => 'event',
            'sub_menu' => 'event',
            'event' => Event::find($id),
            'attendance' => $attendance,
            'user_attendances' => EventUser::where('event_id', $id)->with(['user'])->get()->map(function ($user) use ($attendance) {
                $attendanceUser = $attendance->attendances->where('event_user_id', $user->id)->first();
                return (object)[
                    'user' => $user,
                    'attendance' => $attendanceUser ? $attendanceUser : null,
                ];
            }),
        ];
        // return response()->json($data);
        return view('back.pages.event.detail.attendance-detail', $data);
    }

    public function attendanceDetailDatatable(Request $request, $id, $attendanceId)
    {
        $name = $request->name;
        $attendance = EventAttendance::with(['attendances'])->find($attendanceId);
        if (!$attendance) {
            return response()->json(['error' => 'Daftar kehadiran tidak ditemukan'], 404);
        }

        $userAttendances = EventUser::where('event_id', $id)->with(['user'])->get()->map(function ($user) use ($attendance) {
            $attendanceUser = $attendance->attendances->where('event_user_id', $user->id)->first();
            return (object)[
                'user' => $user,
                'attendance' => $attendanceUser ? $attendanceUser : null,
            ];
        })->when($name, function ($query) use ($name) {
            return $query->filter(function ($userAttendance) use ($name) {
                return str_contains(strtolower($userAttendance->user->name), strtolower($name));
            });
        });

        return datatables()
            ->of($userAttendances)
            ->addColumn('user', function ($userAttendance) {
                if (!$userAttendance->user || !$userAttendance->user->user) {
                    return '<span class="text-danger">Pengguna Tidak Ada</span>';
                }
                return '
                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                        <a href="#">
                            <div class="symbol-label">
                                <img src="' . $userAttendance->user->user->getPhoto() . '"
                                    alt="Photo" width="50px" />
                            </div>
                        </a>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="#"
                            class="text-gray-800 text-hover-primary mb-1">' . $userAttendance->user->user->name . '</a>
                        <span>' . $userAttendance->user->email . '</span>
                    </div>
                ';
            })
            ->addColumn('name', function ($userAttendance) {
                return $userAttendance->user->name;
            })
            ->addColumn('email', function ($userAttendance) {
                return $userAttendance->user->email;
            })
            ->addColumn('phone', function ($userAttendance) {
                return $userAttendance->user->phone ?? '-';
            })
            ->addColumn('attendance', function ($userAttendance) {
                if ($userAttendance->attendance) {
                    $ipaddress = '';
                    if ($userAttendance->attendance->ip_address) {
                        $ipaddress = 'IP: ' . $userAttendance->attendance->ip_address . '<br>';
                    }
                    $userAgent = '';
                    if ($userAttendance->attendance->user_agent) {
                        $userAgent = 'Agent: ' . $userAttendance->attendance->user_agent . '<br>';
                    }
                    return '
                        <span class="badge badge-success">Hadir</span><br>
                        waktu: ' . \Carbon\Carbon::parse($userAttendance->attendance->attendance_datetime)->format('d M Y H:i') . '<br>
                        ' . $ipaddress . '
                        ' . $userAgent . '
                    ';
                } else {
                    return '<span class="badge badge-danger">Tidak Hadir</span>';
                }
            })
            ->addColumn('action', function ($userAttendance) use ($attendanceId) {
                if (!$userAttendance->attendance) {
                    return '
                        <button id="checkin-button-' . $userAttendance->user->id . '" link="' . route('back.event.detail.attendance.detail.checkin', [$userAttendance->user->event_id, $attendanceId, $userAttendance->user->id]) . '" class="btn btn-sm btn-light-primary">
                            Buat Hadir
                        </button>
                    ';
                } else {
                    return '-';
                }
            })
            ->rawColumns(['user', 'attendance', 'action'])
            ->make(true);
    }

    public function attendanceExport($id, $attendanceId)
    {
        return Excel::download(
            new EventAttendanceUserExport($id, $attendanceId),
            'kehadiran-event.xlsx'
        );
    }

    public function attendanceDetailUserCheckin(Request $request, $id, $attendanceId, $eventUserId)
    {
        $attendance = EventAttendance::find($attendanceId);
        if (!$attendance) {
            return response()->json(['error' => 'Daftar kehadiran tidak ditemukan'], 404);
        }

        $eventUser = EventUser::find($eventUserId);
        if (!$eventUser) {
            return response()->json(['error' => 'Peserta tidak ditemukan'], 404);
        }

        // Check if the user has already checked in
        if ($attendance->attendances->where('event_user_id', $eventUser->id)->count() > 0) {
            Alert::error('Error', 'Peserta sudah melakukan check-in');
            return redirect()->route('back.event.attendance.detail', ['id' => $id, 'attendance_id' => $attendanceId]);
        }

        // Create attendance record
        $attendanceUser = new EventAttendanceUser();
        $attendanceUser->event_attendance_id = $attendance->id;
        $attendanceUser->event_user_id = $eventUser->id;
        $attendanceUser->attendance_datetime = now();
        $attendanceUser->save();

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil melakukan check-in',
            'attendance' => $attendanceUser
        ]);
    }

    public function notification($id)
    {
        $data = [
            'title' => 'Notifikasi event',
            'menu' => 'event',
            'sub_menu' => 'event',
            'event' => Event::find($id),
        ];

        return view('back.pages.event.detail.notification', $data);
    }

    public function notificationWhatsapp(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'message' => 'required',
            'attachment' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:8192',
        ], [
            'required' => ':attribute harus diisi',
            'mimes' => 'File harus berupa gambar atau dokumen',
            'max' => 'Ukuran file maksimal 8MB',
        ]);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $fileName = null;
        $filePath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = 'lampiran_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('event/attachments', $fileName, 'public');
            $filePath = asset('storage/event/attachments/' . $fileName);
        }

        $mailEnvirontment = env('MAIL_ENVIRONMENT', 'local');
        if ($mailEnvirontment == 'local') {
            $fileName = "Kamen_rider_eurodata.png";
            $filePath = "https://upload.wikimedia.org/wikipedia/id/b/b0/Kamen_rider_eurodata.png";
        }

        $data = [];
        if ($request->user == 'all') {
            $user = EventUser::where('event_id', $id)->with(['user'])->get();
            if ($user->isEmpty()) {
                Alert::error('Error', 'Tidak ada peserta yang ditemukan untuk notifikasi ini');
                return redirect()->back();
            }
            foreach ($user as $u) {
                if ($request->hasFile('attachment')) {
                    $data[] = [
                        'to' => whatsappNumber($u->user->phone),
                        'caption' => $request->message,
                        'urlDocument' => $filePath,
                        'fileName' => $fileName,
                    ];
                } else {
                    $data[] = [
                        'to' => whatsappNumber($u->phone),
                        'text' => $request->message,

                    ];
                }
            }
        }

        $response = Http::timeout(60)->post(env('WHATSAPP_API_URL')  . "/send-bulk-message", [
            'session' => env('WHATSAPP_API_SESSION'), // Use the session name from your environment variable
            'delay' => $request->delay,
            'data' => $data
        ]);

        if ($response->status() != 200) {
            Alert::error('Error', 'Failed to send bulk message: ' . $response->json()['message'] ?? 'Unknown error');
            return redirect()->back()->with('error', 'Failed to send bulk message: ' . $response->json()['message'] ?? 'Unknown error');
        }

        Alert::success('Sukses', 'Notifikasi berhasil dikirim');
        return redirect()->back()->with('success', 'Notifikasi berhasil dikirim');
    }
}
