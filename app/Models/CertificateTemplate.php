<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'event_id',
        'template_image',
        'orientation',
        'paper_size',
        'text_elements',
        'signature_image',
        'signature_position',
        'is_active',
        'is_default',
        'created_by',
    ];

    protected $casts = [
        'text_elements' => 'array',
        'signature_position' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get the event that owns this template.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who created this template.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the template image URL.
     */
    public function getTemplateImageUrl()
    {
        if ($this->template_image) {
            return asset('storage/' . $this->template_image);
        }
        return asset('ext_images/template_sertifikat.png');
    }

    /**
     * Get the signature image URL.
     */
    public function getSignatureImageUrl()
    {
        if ($this->signature_image) {
            return asset('storage/' . $this->signature_image);
        }
        return asset('ext_images/ttd_firdaus.png');
    }

    /**
     * Get default text elements for new template.
     */
    public static function getDefaultTextElements()
    {
        return [
            [
                'id' => 'certificate_number',
                'label' => 'Nomor Sertifikat',
                'placeholder' => '{certificate_number}',
                'x' => 18,
                'y' => 63,
                'fontSize' => 15,
                'fontWeight' => 'normal',
                'color' => '#424242',
                'textAlign' => 'left',
                'unit' => 'mm',
            ],
            [
                'id' => 'participant_name',
                'label' => 'Nama Peserta',
                'placeholder' => '{participant_name}',
                'x' => 125,
                'y' => 100,
                'fontSize' => 36,
                'fontWeight' => 'bold',
                'color' => '#f83292',
                'textAlign' => 'left',
                'unit' => 'mm',
            ],
            [
                'id' => 'role_text',
                'label' => 'Teks Peran',
                'placeholder' => 'Sebagai Peserta dalam Kegiatan',
                'x' => 125,
                'y' => 115,
                'fontSize' => 16,
                'fontWeight' => 'normal',
                'color' => '#000000',
                'textAlign' => 'left',
                'unit' => 'mm',
            ],
            [
                'id' => 'event_name',
                'label' => 'Nama Event',
                'placeholder' => '{event_name}',
                'x' => 125,
                'y' => 130,
                'fontSize' => 24,
                'fontWeight' => 'bold',
                'color' => '#005f73',
                'textAlign' => 'left',
                'unit' => 'mm',
            ],
            [
                'id' => 'event_date',
                'label' => 'Tanggal Event',
                'placeholder' => '{event_date}',
                'x' => 125,
                'y' => 145,
                'fontSize' => 16,
                'fontWeight' => 'normal',
                'color' => '#000000',
                'textAlign' => 'left',
                'unit' => 'mm',
            ],
        ];
    }
}
