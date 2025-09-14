<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Editor;
use App\Models\EditorData;
use App\Models\Journal;
use App\Models\PaymentAccount;
use App\Models\Reviewer;
use App\Models\ReviewerData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

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

    public function journalupdate(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'author_fee' => 'required|numeric',
            'akreditasi' => 'nullable',
            'editor_chief_name' => 'nullable|string|max:255',
            'editor_chief_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:8192',
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
        $journal->name = $request->name;
        $journal->author_fee = $request->author_fee;
        $journal->indexing = $request->akreditasi;
        $journal->editor_chief_name = $request->editor_chief_name;
        if ($request->hasFile('editor_chief_signature')) {
            $file = $request->file('editor_chief_signature');
            $filePath = $file->storeAs('journal-signature', Str::random(10) . '.' . $file->getClientOriginalExtension(), 'public');
            $journal->editor_chief_signature = $filePath;
        }

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
        $validator = Validator::make(
            $request->all(),
            [
                'payment_accounts' => 'array',
                'payment_accounts.*.account_name' => 'required|string|max:255',
                'payment_accounts.*.account_number' => 'required|string|max:255',
                'payment_accounts.*.bank' => 'required|string|max:255',
            ],
            [
                'payment_accounts.*.account_name.required' => 'Nama pemilik rekening tidak boleh kosong',
                'payment_accounts.*.account_number.required' => 'Nomor rekening tidak boleh kosong',
                'payment_accounts.*.bank.required' => 'Nama bank tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }



        if ($request->delete_account) {
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

    public function reviewerIndex()
    {
        $data = [
            'title' => 'Reviewer',
            'breadcrumbs' => [
                [
                    'name' => 'Reviewer',
                    'link' => route('back.master.reviewer.index')
                ]
            ],
            'reviewers' => Reviewer::with(['data'])
                ->get()
                ->unique('reviewer_id')
                ->map(function ($reviewer) {
                    $reviewer->journal = Reviewer::where('reviewer_id', $reviewer->reviewer_id)
                        ->with('issue.journal')
                        ->get()
                        ->map(function ($item) {
                            $journal_data = $item->issue->journal;
                            return (object) [
                                'id' => $journal_data->id,
                                'name' => $journal_data->name,
                                'title' => $journal_data->title,
                                'url_path' => $journal_data->url_path,
                            ];
                        });
                    return $reviewer;
                }),
        ];
        // return response()->json($data);
        return view('back.pages.master.reviewer.index', $data);
    }

    public function reviewerUpdate(Request $request, $id)
    {
        $reviewer = Reviewer::where('reviewer_id', $id)->first();
        if (!$reviewer) {
            Alert::error('Gagal', 'Reviewer tidak ditemukan');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:255',
            'account_bank' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ReviewerData::updateOrCreate(
            ['reviewer_id' => $reviewer->reviewer_id],
            [
                'nik' => $request->nik,
                'account_bank' => $request->account_bank,
                'account_number' => $request->account_number,
                'npwp' => $request->npwp,
            ]
        );

        Alert::success('Success', 'Reviewer has been updated');
        return redirect()->back();
    }

    public function editorIndex()
    {
        $data = [
            'title' => 'Editor',
            'breadcrumbs' => [
                [
                    'name' => 'Editor',
                    'link' => route('back.master.editor.index')
                ]
            ],
            'editors' => Editor::with(['data'])
                ->get()
                ->unique('editor_id')
                ->map(function ($editor) {
                    $editor->journal = Editor::where('editor_id', $editor->editor_id)
                        ->with('issue.journal')
                        ->get()
                        ->map(function ($item) {
                            $journal_data = $item->issue->journal;
                            return (object) [
                                'id' => $journal_data->id,
                                'name' => $journal_data->name,
                                'title' => $journal_data->title,
                                'url_path' => $journal_data->url_path,
                            ];
                        });
                    return $editor;
                }),
        ];

        return view('back.pages.master.editor.index', $data);
    }

    public function editorUpdate(Request $request, $id)
    {
        $editor = Editor::where('editor_id', $id)->first();
        if (!$editor) {
            Alert::error('Gagal', 'Editor tidak ditemukan');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:255',
            'account_bank' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        EditorData::updateOrCreate(
            ['editor_id' => $editor->editor_id],
            [
                'nik' => $request->nik,
                'account_bank' => $request->account_bank,
                'account_number' => $request->account_number,
                'npwp' => $request->npwp,
            ]
        );

        Alert::success('Success', 'Editor has been updated');
        return redirect()->back();
    }
}
