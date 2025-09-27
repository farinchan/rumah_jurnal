<?php

namespace App\Observers;

use App\Models\Journal;
use App\Models\PaymentAccount;
use App\Models\PaymentInvoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentInvoiceObserver
{
    /**
     * Handle the PaymentInvoice "created" event.
     */
    public function created(PaymentInvoice $paymentInvoice): void
    {
        // $jurnal = Journal::where('url_path', $paymentInvoice->submission->issue->journal->url_path)->first();
        // $paymentAccount = PaymentAccount::first();

        // if (!$jurnal) {
        //     Log::error('Journal not found for PaymentInvoice ID: ' . $paymentInvoice->id);
        //     return;
        // }

        // try {
        //     $response1 = Http::withHeaders([
        //         'Accept' => 'application/json',
        //         'Authorization' => 'Bearer ' . $jurnal->api_key
        //     ])->get($jurnal->url . '/api/v1/submissions/' . $paymentInvoice->submission->submission_id . '/participants', [
        //         'apiToken' => $jurnal->api_key
        //     ]);

        //     if ($response1->status() === 200) {
        //         $data1 = $response1->json();
        //         $response2 = Http::withHeaders([
        //             'Accept' => 'application/json',
        //             'Authorization' => 'Bearer ' . $jurnal->api_key
        //         ])->get($data1[0]['_href'], [
        //             'apiToken' => $jurnal->api_key
        //         ]);
        //         if ($response2->status() === 200) {
        //             $data2 = $response2->json();

        //             $response_wa = Http::post(env('WHATSAPP_API_URL')  . "/send-message", [
        //                 'session' => env('WHATSAPP_API_SESSION'),
        //                 'to' => whatsappNumber($data2["phone"]),
        //                 'text' => "Halo Bapak/Ibu " . ($data2["fullName"] ?? '-') . "\n\n" .
        //                     "Invoice untuk untuk pembayaran artikel Anda dengan *SUBMISSION ID: " . $paymentInvoice->submission->submission_id . "* telah terbit. Berikut adalah detail invoice Anda:\n\n" .
        //                     "INVOICE: " . ($paymentInvoice->invoice_number ?? "0000") . "/JRNL/UINSMDD/" . ($paymentInvoice->created_at->format('Y') ?? Carbon::now()->format('Y')) . "\n" .
        //                     "Jumlah: Rp " . number_format($paymentInvoice->payment_amount, 0, ',', '.') . "\n" .
        //                     "Persentase Pembayaran: " . ($paymentInvoice->payment_percent ?? '-') . "%\n" .

        //                     "Silakan lakukan pembayaran sesuai dengan jumlah yang tertera pada invoice. pembayaran dapat dilakukan melalui transfer ke rekening berikut:\n" .
        //                     "Bank: " . ($paymentAccount->bank ?? '-') . "\n" .
        //                     "Nomor Rekening: " . ($paymentAccount->account_number ?? '-') . "\n" .
        //                     "Atas Nama: " . ($paymentAccount->account_name ?? '-') . "\n\n" .

        //                     "berikut kami lampirkan file invoice kepada anda, jika file tidak terkirim anda dapat mengunduhnya melalui tautan berikut:\n" .
        //                     asset('storage/arsip/invoice/' . $paymentInvoice->created_at->format('Y') . '/' . $paymentInvoice->invoice_number . '/invoice-' . $paymentInvoice->submission->submission_id .  '-' . $paymentInvoice->submission->authors[0]['id'] . '.pdf') . "\n\n" .

        //                     "batas waktu pembayaran anda adalah " . \Carbon\Carbon::parse($paymentInvoice->payment_due_date)->translatedFormat('d F Y') . ". Setelah melakukan pembayaran, silakan unggah bukti pembayaran melalui tautan berikut:\n" .
        //                     route('payment.pay', [ $paymentInvoice->submission->issue->journal->url_path, $paymentInvoice->submission->submission_id]) . "\n\n" .
        //                     "Terima kasih atas perhatian dan kerjasama Anda " .

        //                     "Salam,\n" .
        //                     "Editorial Rumah Jurnal\n\n" .

        //                     "_generate by system_\n" .
        //                     url('/')

        //             ]);
        //             if ($response_wa->status() === 200) {
        //                 Log::info('WhatsApp message sent successfully to ' . $data2["phone"]);
        //             } else {
        //                 Log::error('Error sending WhatsApp message: ' . $response_wa->body());
        //             }
        //         } else {
        //             Log::error('Error PaymentInvoiceObserver Response 2: ' . $response2->body());
        //         }
        //     } else {
        //         Log::error('Error PaymentInvoiceObserver Response 1: ' . $response1->body());
        //     }
        // } catch (\Throwable $th) {
        //     Log::error('Error PaymentInvoiceObserver TryCatch: ' . $th->getMessage());
        // }
    }

    /**
     * Handle the PaymentInvoice "updated" event.
     */
    public function updated(PaymentInvoice $paymentInvoice): void
    {
        //
    }

    /**
     * Handle the PaymentInvoice "deleted" event.
     */
    public function deleted(PaymentInvoice $paymentInvoice): void
    {
        //
    }

    /**
     * Handle the PaymentInvoice "restored" event.
     */
    public function restored(PaymentInvoice $paymentInvoice): void
    {
        //
    }

    /**
     * Handle the PaymentInvoice "force deleted" event.
     */
    public function forceDeleted(PaymentInvoice $paymentInvoice): void
    {
        //
    }
}
