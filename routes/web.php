<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\AnnouncementController;
use App\Http\Controllers\Front\EventController;
use App\Http\Controllers\Front\NewsController;
use App\Http\Controllers\Front\JournalController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Back\DashboardController as BackDashboardController;
use App\Http\Controllers\Back\AnnouncementController as BackAnnouncementController;
use App\Http\Controllers\Back\EventController as BackEventController;
use App\Http\Controllers\Back\NewsController as BackNewsController;
use App\Http\Controllers\Back\WelcomeSpeechController as BackWelcomeSpeechController;
use App\Http\Controllers\Back\JournalController as BackJournalController;
use App\Http\Controllers\Back\FinanceController as BackFinanceController;
use App\Http\Controllers\Back\MasterdataController as BackMasterDataController;
use App\Http\Controllers\Back\UserController as BackUserController;
use App\Http\Controllers\Back\MessageController as BackMessageController;
use App\Http\Controllers\Back\SettingController as BackSettingController;

Route::get('/locale/{locale}', LocaleController::class)->name('locale.change');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/welcome', [HomeController::class, 'welcomeSpeech'])->name('welcome.speech');

Route::prefix('event')->name('event.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
});

Route::prefix('announcement')->name('announcement.')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index'])->name('index');
    Route::get('/{slug}', [AnnouncementController::class, 'show'])->name('show');
});

Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{slug}', [NewsController::class, 'detail'])->name('detail');

    Route::get('/category/{slug}', [NewsController::class, 'category'])->name('category');
    Route::post('/comment', [NewsController::class, 'comment'])->name('comment');
});

Route::prefix('journal')->name('journal.')->group(function () {
    Route::get('/', [JournalController::class, 'index'])->name('index');
    Route::get('/{journal_path}', [JournalController::class, 'detail'])->name('detail');
});

Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/{journal_path}/submission/{submission_id}', [PaymentController::class, 'submission'])->name('submission');
    Route::get('/{journal_path}/submission/{submission_id}/pay', [PaymentController::class, 'pay'])->name('pay');
    Route::post('/{journal_path}/submission/{submission_id}/pay', [PaymentController::class, 'payStore'])->name('pay.store');
});

Route::prefix('contact')->name('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'send'])->name('send');
});

Route::prefix('back')->name('back.')->middleware('auth')->group(function () {


    Route::get('/dashboard', [BackDashboardController::class, 'index'])->name('dashboard');
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/visitor-stat', [BackDashboardController::class, 'visistorStat'])->name('visitor.stat');

        Route::get('/news', [BackDashboardController::class, 'news'])->name('news');
        Route::get('/news-stat', [BackDashboardController::class, 'stat'])->name('news.stat');
    });

    Route::prefix('announcement')->name('announcement.')->group(function () {
        Route::get('/', [BackAnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [BackAnnouncementController::class, 'create'])->name('create');
        Route::post('/create', [BackAnnouncementController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BackAnnouncementController::class, 'edit'])->name('edit');
        Route::put('/edit/{id}', [BackAnnouncementController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BackAnnouncementController::class, 'destroy'])->name('destroy');
    });

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

    Route::prefix('welcomeSpeech')->name('welcomeSpeech.')->group(function () {
        Route::get('/', [BackWelcomeSpeechController::class, 'index'])->name('index');
        Route::put('/edit', [BackWelcomeSpeechController::class, 'update'])->name('update');
    });

    Route::prefix('journal')->name('journal.')->group(function () {
        Route::get('/{journal_path}', [BackJournalController::class, 'index'])->name('index');

        Route::post('/{journal_path}/issue/store', [BackJournalController::class, 'issueStore'])->name('issue.store');
        Route::put('/{journal_path}/issue/{issue_id}/update', [BackJournalController::class, 'issueUpdate'])->name('issue.update');
        Route::delete('/{journal_path}/issue/{issue_id}/delete', [BackJournalController::class, 'issueDestroy'])->name('issue.destroy');

        Route::get('/{journal_path}/issue/{issue_id}/dashboard', [BackJournalController::class, 'dashboardIndex'])->name('dashboard.index');

        Route::get('/{journal_path}/issue/{issue_id}/article', [BackJournalController::class, 'articleIndex'])->name('article.index');
        Route::delete('/{journal_path}/issue/{issue_id}/article/{id}/destroy', [BackJournalController::class, 'articleDestroy'])->name('article.destroy');

        Route::get('/{journal_path}/issue/{issue_id}/reviewer', [BackJournalController::class, 'reviewerIndex'])->name('reviewer.index');
        Route::delete('/{journal_path}/issue/{issue_id}/reviewer/{id}/delete', [BackJournalController::class, 'reviewerDestroy'])->name('reviewer.destroy');

        Route::get('/{journal_path}/issue/{issue_id}/setting', [BackJournalController::class, 'settingIndex'])->name('setting.index');
    });




    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/verification', [BackFinanceController::class, 'verificationIndex'])->name('verification.index');
        Route::get('/verification/datatable', [BackFinanceController::class, 'verificationDatatable'])->name('verification.datatable');
        Route::get('/report', [BackFinanceController::class, 'reportIndex'])->name('report.index');
    });

    Route::prefix('master')->name('master.')->group(function () {

        Route::prefix('journal')->name('journal.')->group(function () {
            Route::get('/', [BackMasterDataController::class, 'journalIndex'])->name('index');
            Route::put('/edit/{id}', [BackMasterDataController::class, 'journalUpdate'])->name('update');
            Route::delete('/delete/{id}', [BackMasterDataController::class, 'journalDestroy'])->name('destroy');
        });

        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [BackUserController::class, 'index'])->name('index');
            Route::get('/create', [BackUserController::class, 'create'])->name('create');
            Route::post('/create', [BackUserController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [BackUserController::class, 'edit'])->name('edit');
            Route::put('/edit/{id}', [BackUserController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [BackUserController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('message')->name('message.')->group(function () {
        Route::get('/', [BackMessageController::class, 'index'])->name('index');
        Route::delete('/{id}', [BackMessageController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('setting')->name('setting.')->group(function () {
        Route::get('/website', [BackSettingController::class, 'website'])->name('website');
        Route::put('/website', [BackSettingController::class, 'websiteUpdate'])->name('website.update');
        Route::put('/website/info', [BackSettingController::class, 'informationUpdate'])->name('website.info');

        Route::get('/banner', [BackSettingController::class, 'banner'])->name('banner');
        Route::put('/banner/{id}/update', [BackSettingController::class, 'bannerUpdate'])->name('banner-update');
    });
});
