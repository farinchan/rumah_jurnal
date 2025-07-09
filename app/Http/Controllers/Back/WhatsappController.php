<?php

namespace App\Http\Controllers\back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function setting()
    {
        $data = [
            'title' => 'Pengaturan Whatsapp',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Pengaturan',
                    'link' => route('back.whatsapp.setting')
                ]
            ],
        ];

        return view('back.pages.whatsapp.setting', $data);

    }

    public function message()
    {
        $data = [
            'title' => 'Pesan Whatsapp',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Pesan Whatsapp',
                    'link' => route('back.whatsapp.message')
                ]
            ],
        ];

        return view('back.pages.whatsapp.message', $data);
    }
}
