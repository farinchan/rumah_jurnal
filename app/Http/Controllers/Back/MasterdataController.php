<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class MasterdataController extends Controller
{
    public function journalIndex()
    {
        $data = [
            'title' => 'Jurnal',
            'breadcrumbs' => [
                [
                    'name' => 'Jurnal',
                    'link' => route('back.master.journal.index')
                ]
            ],
            'journals' => Journal::all()
        ];

        // return response()->json($data);
        return view('back.pages.master.journal.index', $data);
    }

    public function journalupdate(Request $reques, $id)
    {
        // dd($reques->all());
        $validator = Validator::make($reques->all(), [
            'name' => 'required|string|max:255',
            'author_fee' => 'required|numeric',
            'akreditasi' => 'nullable',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $journal = Journal::find($id);
        if (!$journal) {
            Alert::error('Gagal', 'Jurnal tidak ditemukan');
            return redirect()->back();
        }
        $journal->name = $reques->name;
        $journal->author_fee = $reques->author_fee;
        $journal->indexing = $reques->akreditasi;
        $journal->save();

        Alert::success('Berhasil', 'Data berhasil diubah');
        return redirect()->back();
    }

    public function journalDestroy($id)
    {
        $journal = Journal::find($id);
        $journal->delete();

        Alert::success('Berhasil', 'Data berhasil dihapus');
        return redirect()->back();
    }

    public function paymentAccount()
    {
        $data = [
            'title' => 'Rekening Pembayaran',
            'breadcrumbs' => [
                [
                    'name' => 'Rekening Pembayaran',
                    'link' => route('back.master.payment-account.index')
                ]
            ],
            'payment_accounts' => PaymentAccount::all()
        ];

        return view('back.pages.master.payment-account.index', $data);
    }

    public function paymentAccountUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_accounts' => 'array',
            'payment_accounts.*.account_name' => 'required|string|max:255',
            'payment_accounts.*.account_number' => 'required|string|max:255',
            'payment_accounts.*.bank' => 'required|string|max:255',
        ],
        [
            'payment_accounts.*.account_name.required' => 'Nama pemilik rekening tidak boleh kosong',
            'payment_accounts.*.account_number.required' => 'Nomor rekening tidak boleh kosong',
            'payment_accounts.*.bank.required' => 'Nama bank tidak boleh kosong',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

    

        if($request->delete_account) {
            $account_delete = json_decode($request->delete_account, true);
            foreach ($account_delete as $accountId) {
                $account = PaymentAccount::find($accountId);
                if ($account) {
                    $account->delete();
                }
            }
        }

        if ($request->payment_accounts) {
            foreach ($request->payment_accounts as $accountData) {
                $accountName = $accountData['account_name'] ?? '-';
                $accountNumber = $accountData['account_number'] ?? '-';
                $accountBank = $accountData['bank'] ?? '-';

                // Jika ada ID sertifikat, berarti update data lama
                if (isset($accountData['account_id'])) {
                    $account = PaymentAccount::find($accountData['account_id']);

                    if (!$account) continue;

                    // Update informasi lainnya
                    $account->account_name = $accountName;
                    $account->account_number = $accountNumber;
                    $account->bank = $accountBank;
                    $account->save();
                } else {
                    // Jika tidak ada ID, buat data baru
                    $account = new PaymentAccount();
                    $account->account_name = $accountName;
                    $account->account_number = $accountNumber;
                    $account->bank = $accountBank;
                    $account->save();
                }
            }
        }

        Alert::success('Berhasil', 'Data berhasil diubah');
        return redirect()->back();
    }
}
