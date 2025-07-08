<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}");
    }

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'payment_timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function paymentInvoice()
    {
        return $this->hasOne(PaymentInvoice::class, 'id', 'payment_invoice_id');
    }

    public function paymentProofs()
    {
        return Storage::url($this->payment_file)?? '';
    }

}
