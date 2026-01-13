<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use App\Models\Event;
use App\Models\EventUser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class CertificateController extends Controller
{
    /**
     * Display certificate template for an event (1 event = 1 template).
     */
    public function index($eventId)
    {
        $event = Event::findOrFail($eventId);
        $template = CertificateTemplate::where('event_id', $eventId)->first();

        // If template exists, redirect to edit page
        if ($template) {
            return redirect()->route('back.event.detail.certificate.edit', [$eventId, $template->id]);
        }

        // If no template, show create page
        return redirect()->route('back.event.detail.certificate.create', $eventId);
    }

    /**
     * Show the form for creating a new certificate template.
     */
    public function create($eventId)
    {
        $event = Event::findOrFail($eventId);

        // Check if template already exists for this event
        $existingTemplate = CertificateTemplate::where('event_id', $eventId)->first();
        if ($existingTemplate) {
            return redirect()->route('back.event.detail.certificate.edit', [$eventId, $existingTemplate->id]);
        }

        $defaultElements = CertificateTemplate::getDefaultTextElements();

        $data = [
            'title' => 'Buat Template Sertifikat - ' . $event->name,
            'menu' => 'event',
            'sub_menu' => 'event',
            'event' => $event,
            'defaultElements' => $defaultElements,
        ];

        return view('back.pages.event.detail.certificate.create', $data);
    }

    /**
     * Store a newly created certificate template.
     */
    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'template_image' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            'orientation' => 'required|in:landscape,portrait',
            'paper_size' => 'required|in:A4,Letter,Legal',
            'signature_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'text_elements' => 'nullable|json',
            'signature_position' => 'nullable|json',
        ], [
            'name.required' => 'Nama template harus diisi',
            'template_image.required' => 'Template gambar harus diupload',
            'template_image.image' => 'File harus berupa gambar',
            'template_image.max' => 'Ukuran gambar maksimal 10MB',
            'orientation.required' => 'Orientasi harus dipilih',
            'paper_size.required' => 'Ukuran kertas harus dipilih',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $template = new CertificateTemplate();
        $template->name = $request->name;
        $template->event_id = $eventId;
        $template->orientation = $request->orientation;
        $template->paper_size = $request->paper_size;
        $template->created_by = Auth::id();

        // Upload template image
        if ($request->hasFile('template_image')) {
            $file = $request->file('template_image');
            $fileName = 'certificate_template_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('certificates/templates', $fileName, 'public');
            $template->template_image = $filePath;
        }

        // Upload signature image
        if ($request->hasFile('signature_image')) {
            $file = $request->file('signature_image');
            $fileName = 'signature_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('certificates/signatures', $fileName, 'public');
            $template->signature_image = $filePath;
        }

        // Set text elements - start empty
        if ($request->text_elements) {
            $template->text_elements = json_decode($request->text_elements, true);
        } else {
            $template->text_elements = []; // Start with empty elements
        }

        // Set signature position
        if ($request->signature_position) {
            $template->signature_position = json_decode($request->signature_position, true);
        }

        $template->is_active = true;
        $template->save();

        Alert::success('Sukses', 'Template sertifikat berhasil dibuat');
        return redirect()->route('back.event.detail.certificate.edit', [$eventId, $template->id]);
    }

    /**
     * Show the form for editing a certificate template with drag & drop.
     */
    public function edit($eventId, $templateId)
    {
        $event = Event::findOrFail($eventId);
        $template = CertificateTemplate::findOrFail($templateId);

        // Get sample participant for preview
        $sampleParticipant = EventUser::where('event_id', $eventId)->first();

        $data = [
            'title' => 'Edit Template Sertifikat - ' . $event->name,
            'menu' => 'event',
            'sub_menu' => 'event',
            'event' => $event,
            'template' => $template,
            'sampleParticipant' => $sampleParticipant,
        ];

        return view('back.pages.event.detail.certificate.edit', $data);
    }

    /**
     * Update the certificate template (including positions via AJAX).
     */
    public function update(Request $request, $eventId, $templateId)
    {
        try {
            $template = CertificateTemplate::findOrFail($templateId);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'template_image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
                'orientation' => 'sometimes|required|in:landscape,portrait',
                'paper_size' => 'sometimes|required|in:A4,Letter,Legal',
                'signature_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'text_elements' => 'nullable',
                'signature_position' => 'nullable',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal: ' . $validator->errors()->first(),
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            if ($request->has('name') && $request->name) {
                $template->name = $request->name;
            }
            if ($request->has('orientation') && $request->orientation) {
                $template->orientation = $request->orientation;
            }
            if ($request->has('paper_size') && $request->paper_size) {
                $template->paper_size = $request->paper_size;
            }

            // Upload new template image
            if ($request->hasFile('template_image')) {
                // Delete old file
                if ($template->template_image && Storage::disk('public')->exists($template->template_image)) {
                    Storage::disk('public')->delete($template->template_image);
                }
                $file = $request->file('template_image');
                $fileName = 'certificate_template_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('certificates/templates', $fileName, 'public');
                $template->template_image = $filePath;
            }

            // Upload new signature image
            if ($request->hasFile('signature_image')) {
                // Delete old file
                if ($template->signature_image && Storage::disk('public')->exists($template->signature_image)) {
                    Storage::disk('public')->delete($template->signature_image);
                }
                $file = $request->file('signature_image');
                $fileName = 'signature_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('certificates/signatures', $fileName, 'public');
                $template->signature_image = $filePath;
            }

            // Update text elements
            if ($request->has('text_elements')) {
                $textElements = $request->text_elements;
                if (is_string($textElements)) {
                    $textElements = json_decode($textElements, true);
                }
                $template->text_elements = $textElements;
            }

            // Update signature position
            if ($request->has('signature_position')) {
                $signaturePosition = $request->signature_position;
                if (is_string($signaturePosition)) {
                    $signaturePosition = json_decode($signaturePosition, true);
                }
                $template->signature_position = $signaturePosition;
            }

            $template->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template berhasil disimpan',
                    'template' => $template
                ]);
            }

            Alert::success('Sukses', 'Template sertifikat berhasil diperbarui');
            return redirect()->route('back.event.detail.certificate.index', $eventId);
        } catch (\Exception $e) {
            Log::error('Certificate update error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            Alert::error('Error', 'Terjadi kesalahan saat menyimpan template');
            return redirect()->back();
        }
    }

    /**
     * Save positions via AJAX (auto-save).
     */
    public function savePositions(Request $request, $eventId, $templateId)
    {
        try {
            $template = CertificateTemplate::findOrFail($templateId);

            if ($request->has('text_elements')) {
                $textElements = $request->text_elements;
                if (is_string($textElements)) {
                    $textElements = json_decode($textElements, true);
                }
                $template->text_elements = $textElements;
            }

            if ($request->has('signature_position')) {
                $signaturePosition = $request->signature_position;
                if (is_string($signaturePosition)) {
                    $signaturePosition = json_decode($signaturePosition, true);
                }
                $template->signature_position = $signaturePosition;
            }

            $template->save();

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            Log::error('Certificate save positions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview certificate with current template.
     */
    public function preview($eventId, $templateId)
    {
        $event = Event::findOrFail($eventId);
        $template = CertificateTemplate::findOrFail($templateId);

        // Get sample participant for preview
        $sampleParticipant = EventUser::where('event_id', $eventId)->first();

        // Prepare data for PDF
        $dates = explode(' - ', $event->datetime);
        $before = $dates[0] ?? null;
        $after = $dates[1] ?? null;
        \Carbon\Carbon::setLocale('id');
        $date_before = $before ? \Carbon\Carbon::parse($before)->translatedFormat('l, d F Y') : null;

        $data = [
            'template' => $template,
            'event' => $event,
            'event_name' => $event->name,
            'participant_name' => $sampleParticipant->name ?? 'Nama Peserta Contoh',
            'event_date' => $event->datetime,
            'date_formatted' => $date_before,
            'certificate_number' => str_pad($event->id, 4, '0', STR_PAD_LEFT),
        ];

        $orientation = $template->orientation == 'landscape' ? 'landscape' : 'portrait';
        $pdf = Pdf::loadView('back.pages.event.detail.certificate.pdf', $data)
            ->setPaper($template->paper_size, $orientation);

        return $pdf->stream('preview_sertifikat.pdf');
    }

    /**
     * Delete a certificate template.
     */
    public function destroy($eventId, $templateId)
    {
        $template = CertificateTemplate::findOrFail($templateId);

        // Delete files
        if ($template->template_image && Storage::disk('public')->exists($template->template_image)) {
            Storage::disk('public')->delete($template->template_image);
        }
        if ($template->signature_image && Storage::disk('public')->exists($template->signature_image)) {
            Storage::disk('public')->delete($template->signature_image);
        }

        $template->delete();

        Alert::success('Sukses', 'Template sertifikat berhasil dihapus');
        return redirect()->route('back.event.detail.certificate.create', $eventId);
    }

    /**
     * Generate certificate for a participant.
     */
    public function generateForParticipant($eventId, $eventUserId)
    {
        $event = Event::findOrFail($eventId);
        $eventUser = EventUser::with(['event', 'user'])->findOrFail($eventUserId);

        // Get template for this event (1 event = 1 template)
        $template = CertificateTemplate::where('event_id', $eventId)->first();

        // Prepare data
        $dates = explode(' - ', $event->datetime);
        $before = $dates[0] ?? null;
        $after = $dates[1] ?? null;
        \Carbon\Carbon::setLocale('id');
        $date_before = $before ? \Carbon\Carbon::parse($before)->translatedFormat('l, d F Y') : null;

        $data = [
            'template' => $template,
            'event' => $event,
            'event_name' => $event->name,
            'participant_name' => $eventUser->name ?? $eventUser->user->name ?? 'Peserta',
            'event_date' => $event->datetime,
            'date_formatted' => $date_before,
            'certificate_number' => str_pad($event->id, 4, '0', STR_PAD_LEFT),
        ];

        if ($template) {
            $orientation = $template->orientation == 'landscape' ? 'landscape' : 'portrait';
            $pdf = Pdf::loadView('back.pages.event.detail.certificate.pdf', $data)
                ->setPaper($template->paper_size, $orientation);
        } else {
            // Fallback to old certificate view
            $pdf = Pdf::loadView('front.pages.event.certificate', $data)
                ->setPaper('A4', 'landscape');
        }

        return $pdf->stream('sertifikat_' . $eventUser->id . '.pdf');
    }
}
