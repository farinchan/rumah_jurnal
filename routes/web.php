<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Back\DashboardController as BackDashboardController;
use App\Http\Controllers\Back\EventController as BackEventController;
use App\Http\Controllers\Back\NewsController as BackNewsController;
use App\Http\Controllers\Back\JournalController as BackJournalController;
use App\Http\Controllers\Back\MasterdataController as BackMasterDataController;
use App\Http\Controllers\Back\UserController as BackUserController;
use App\Http\Controllers\Back\MessageController as BackMessageController;
use App\Http\Controllers\Back\SettingController as BackSettingController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


Route::prefix('back')->name('back.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [BackDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('event')->name('event.')->group(function () {
        Route::get('/', [BackEventController::class, 'index'])->name('index');
        Route::get('/create', [BackEventController::class, 'create'])->name('create');
        Route::post('/create', [BackEventController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BackEventController::class, 'edit'])->name('edit');
        Route::put('/edit/{id}', [BackEventController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BackEventController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/category', [BackNewsController::class, 'category'])->name('category');
        Route::post('/category', [BackNewsController::class, 'categoryStore'])->name('category.store');
        Route::put('/category/edit/{id}', [BackNewsController::class, 'categoryUpdate'])->name('category.update');
        Route::delete('/category/delete/{id}', [BackNewsController::class, 'categoryDestroy'])->name('category.destroy');

        Route::get('/', [BackNewsController::class, 'index'])->name('index');
        Route::get('/create', [BackNewsController::class, 'create'])->name('create');
        Route::post('/create', [BackNewsController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BackNewsController::class, 'edit'])->name('edit');
        Route::put('/edit/{id}', [BackNewsController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BackNewsController::class, 'destroy'])->name('destroy');

        Route::get('/comment', [BackNewsController::class, 'comment'])->name('comment');
        Route::post('/comment/spam/{id}', [BackNewsController::class, 'commentSpam'])->name('comment.spam');
    });

    Route::prefix('journal')->name('journal.')->group(function () {
        Route::get('/{journal_path}', [BackJournalController::class, 'index'])->name('index');

        Route::post('/{journal_path}/issue/store', [BackJournalController::class, 'issueStore'])->name('issue.store');
        Route::put('/{journal_path}/issue/{id}/update', [BackJournalController::class, 'issueUpdate'])->name('issue.update');
        Route::delete('/{journal_path}/issue/{id}/delete', [BackJournalController::class, 'issueDestroy'])->name('issue.destroy');

        Route::get('/{journal_path}/issue/{issue_id}/article', [BackJournalController::class, 'articleIndex'])->name('article.index');
    });


    Route::prefix('master')->name('master.')->group(function () {

        Route::prefix('journal')->name('journal.')->group(function () {
            Route::get('/', [BackMasterDataController::class, 'journalIndex'])->name('index');
            Route::put('/edit/{id}', [BackMasterDataController::class, 'journalUpdate'])->name('update');
            Route::delete('/delete/{id}', [BackMasterDataController::class, 'journalDestroy'])->name('destroy');
        });

        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [BackMasterDataController::class, 'UserIndex'])->name('index');
            Route::post('/create', [BackMasterDataController::class, 'userStore'])->name('store');
            Route::put('/edit/{id}', [BackMasterDataController::class, 'userUpdate'])->name('update');
            Route::delete('/delete/{id}', [BackMasterDataController::class, 'userDestroy'])->name('destroy');
        });
    });

    Route::prefix('message')->name('message.')->group(function () {
        Route::get('/', [BackMessageController::class, 'index'])->name('index');
        Route::delete('/{id}', [BackMessageController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('setting')->name('setting.')->group(function () {
        Route::get('/website', [App\Http\Controllers\back\SettingController::class, 'website'])->name('website');
        Route::put('/website', [App\Http\Controllers\back\SettingController::class, 'websiteUpdate'])->name('website.update');
        Route::put('/website/info', [App\Http\Controllers\back\SettingController::class, 'informationUpdate'])->name('website.info');

        Route::get('/banner', [App\Http\Controllers\back\SettingController::class, 'banner'])->name('banner');
        Route::put('/banner/{id}/update', [App\Http\Controllers\back\SettingController::class, 'bannerUpdate'])->name('banner-update');
    });
});
