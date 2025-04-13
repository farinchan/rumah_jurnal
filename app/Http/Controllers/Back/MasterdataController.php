<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Journal;
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
}
