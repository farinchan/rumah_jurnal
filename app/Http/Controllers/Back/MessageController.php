<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MessageController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Pesan',
            'menu' => 'Administrator',
            'submenu' => 'Pesan',
            'list_message' => Message::latest()->get()
        ];
        return view('back.pages.message.index', $data);
    }

    public function destroy($id)
    {
        Message::find($id)->delete();
        return redirect()->back()->with('success', 'Pesan berhasil dihapus');
    }
}
