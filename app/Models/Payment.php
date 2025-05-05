<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
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
