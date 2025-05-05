<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInvoice extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function Submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'payment_invoice_id');
    }
}
