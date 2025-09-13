<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\MenuProfil;
use App\Models\SettingWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuProfilController extends Controller
{


    public function show($slug)
    {
        $setting_web = SettingWebsite::first();
        $menu_profil = MenuProfil::where('slug', $slug)->first();
        $data = [
            'title' => $menu_profil->name,
            'meta' => [
                'title' => $menu_profil->name . ' | ' . $setting_web->name,
                'description' => Str::limit(strip_tags($menu_profil->content), 160),
                'keywords' => $setting_web->name . ', ' . $menu_profil->name .', Journal, Research, OJS System, Open Journal System, Research Journal, Academic Journal, Publication',
                'favicon' => $menu_profil->image ?? $setting_web->favicon
            ],
            'breadcrumbs' => [
                [
                    'name' => __('front.home'),
                    'link' => route('home')
                ],
                [
                    'name' => 'Detail',
                    'link' => route('profil.show', $menu_profil->slug)
                ]
            ],
            'setting_web' => $setting_web,

            'menu_profil' => $menu_profil,
        ];

        return view('front.pages.menu_profil.show', $data);
    }
}
