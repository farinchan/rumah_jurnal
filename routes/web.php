<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\AnnouncementController;
use App\Http\Controllers\Front\MenuProfilController;
use App\Http\Controllers\Front\EventController;
use App\Http\Controllers\Front\NewsController;
use App\Http\Controllers\Front\JournalController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Back\DashboardController as BackDashboardController;
use App\Http\Controllers\Back\AnnouncementController as BackAnnouncementController;
use App\Http\Controllers\Back\EmailController;
use App\Http\Controllers\Back\EventController as BackEventController;
use App\Http\Controllers\Back\NewsController as BackNewsController;
use App\Http\Controllers\Back\WelcomeSpeechController as BackWelcomeSpeechController;
use App\Http\Controllers\Back\journalController as BackJournalController;
use App\Http\Controllers\Back\FinanceController as BackFinanceController;
use App\Http\Controllers\Back\MasterdataController as BackMasterDataController;
use App\Http\Controllers\Back\MenuProfilController as BackMenuProfilController;
use App\Http\Controllers\Back\UserController as BackUserController;
use App\Http\Controllers\Back\MessageController as BackMessageController;
use App\Http\Controllers\Back\SettingController as BackSettingController;
use App\Http\Controllers\Front\AccountController;
use App\Http\Controllers\Front\TeamController;

Route::get('generate-storage', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo 'ok';
});

Route::get('/locale/{locale}', LocaleController::class)->name('locale.change');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/visit', [HomeController::class, 'vistWebsite'])->name('visit.ajax')->middleware('TrustProxies');
Route::get('/welcome-speech', [HomeController::class, 'welcomeSpeech'])->name('welcome.speech');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [HomeController::class, 'termsOfService'])->name('terms.service');


Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('login-cdn');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Forgot Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::prefix('account')->name('account.')->group(function () {
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/password', [AccountController::class, 'password'])->name('password');
    Route::put('/profile/password/update', [AccountController::class, 'passwordUpdate'])->name('password.update');
});

Route::get('/welcome', [HomeController::class, 'welcomeSpeech'])->name('welcome.speech');

Route::prefix('event')->name('event.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
    Route::post('/{slug}/register', [EventController::class, 'register'])->name('register');

    Route::get('/eticket/{uuid}', [EventController::class, 'eticket'])->name('eticket');
    Route::get('/certificate/{uuid}', [EventController::class, 'certificate'])->name('certificate')->middleware('auth');

    Route::get('/presence/{code}', [EventController::class, 'presence'])->name('presence')->middleware('auth');
    Route::post('/presence/{code}/store', [EventController::class, 'presenceStore'])->name('presence.store')->middleware('auth');
});

Route::prefix('announcement')->name('announcement.')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index'])->name('index');
    Route::get('/{slug}', [AnnouncementController::class, 'show'])->name('show');
});

Route::prefix('profil')->name('profil.')->group(function () {
    // Route::get('/', [MenuProfilController::class, 'index'])->name('index');
    Route::get('/{slug}', [MenuProfilController::class, 'show'])->name('show');
});

Route::prefix('team')->name('team.')->group(function () {
    Route::get('/editor', [TeamController::class, 'editor'])->name('editor');
    Route::get('/reviewer', [TeamController::class, 'reviewer'])->name('reviewer');
});

Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{slug}', [NewsController::class, 'detail'])->name('detail');

    Route::get('/category/{slug}', [NewsController::class, 'category'])->name('category');
    Route::post('/comment', [NewsController::class, 'comment'])->name('comment');

    Route::get('/visit/alt', [NewsController::class, 'visit'])->name('visit')->middleware('TrustProxies');
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

        Route::get('/cashflow', [BackDashboardController::class, 'cashflow'])->name('cashflow');
        Route::get('/cashflow-stat', [BackDashboardController::class, 'cashflowStat'])->name('cashflow.stat');
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

        Route::prefix('detail')->name('detail.')->group(function () {
            Route::get('/{id}/overview', [BackEventController::class, 'overview'])->name('overview');

            Route::get('/{id}/participant', [BackEventController::class, 'participant'])->name('participant');
            Route::post('/{id}/participant/store', [BackEventController::class, 'participantStore'])->name('participant.store');
            Route::delete('/{id}/participant/{event_user_id}/delete', [BackEventController::class, 'participantDestroy'])->name('participant.destroy');
            Route::get('/{id}/participant/export', [BackEventController::class, 'participantExport'])->name('participant.export');
            Route::get('/{id}/participant/import-reviewer', [BackEventController::class, 'participantImportReviewerModal'])->name('participant.import-reviewer.modal');
            Route::post('/{id}/participant/import-reviewer', [BackEventController::class, 'participantImportReviewer'])->name('participant.import-reviewer');
            Route::get('/{id}/participant/import-editor', [BackEventController::class, 'participantImportEditorModal'])->name('participant.import-editor.modal');
            Route::post('/{id}/participant/import-editor', [BackEventController::class, 'participantImportEditor'])->name('participant.import-editor');

            Route::get('/{id}/attendance', [BackEventController::class, 'attendance'])->name('attendance');
            Route::post('/{id}/attendance/store', [BackEventController::class, 'attendanceStore'])->name('attendance.store');
            Route::put('/{id}/attendance/{event_attendance_id}/update', [BackEventController::class, 'attendanceUpdate'])->name('attendance.update');
            Route::get('/{id}/attendance/{event_attendance_id}', [BackEventController::class, 'attendanceDetail'])->name('attendance.detail');
            Route::get('/{id}/attendance/{event_attendance_id}/datatable', [BackEventController::class, 'attendanceDetailDatatable'])->name('attendance.detail.datatable');
            Route::post('/{id}/attendance/{event_attendance_id}/checkin/{event_user_id}', [BackEventController::class, 'attendanceDetailUserCheckin'])->name('attendance.detail.checkin');
            Route::get('/{id}/attendance/{event_attendance_id}/export', [BackEventController::class, 'attendanceExport'])->name('attendance.export');

            Route::get('/{id}/notification', [BackEventController::class, 'notification'])->name('notification');
            Route::post('/{id}/notification/whatsapp', [BackEventController::class, 'notificationWhatsapp'])->name('notification.whatsapp');
        });
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

    Route::prefix('menu')->name('menu.')->group(function () {


            Route::prefix('profil')->name('profil.')->group(function () {
                Route::get('/', [BackMenuProfilController::class, 'index'])->name('index');
                Route::post('/create', [BackMenuProfilController::class, 'store'])->name('store');
                Route::get('/edit/{id}', [BackMenuProfilController::class, 'edit'])->name('edit');
                Route::put('/edit/{id}', [BackMenuProfilController::class, 'update'])->name('update');
                Route::delete('/delete/{id}', [BackMenuProfilController::class, 'destroy'])->name('destroy');

                Route::post('/upload', [BackMenuProfilController::class, 'upload'])->name('upload');
            });
        });

    Route::prefix('journal')->name('journal.')->group(function () {
        Route::get('/{journal_path}', [BackJournalController::class, 'index'])->name('index');

        Route::post('/{journal_path}/issue/store', [BackJournalController::class, 'issueStore'])->name('issue.store');
        Route::put('/{journal_path}/issue/{issue_id}/update', [BackJournalController::class, 'issueUpdate'])->name('issue.update');
        Route::delete('/{journal_path}/issue/{issue_id}/delete', [BackJournalController::class, 'issueDestroy'])->name('issue.destroy');

        Route::get('/{journal_path}/issue/{issue_id}/dashboard', [BackJournalController::class, 'dashboardIndex'])->name('dashboard.index');

        Route::get('/{journal_path}/issue/{issue_id}/article', [BackJournalController::class, 'articleIndex'])->name('article.index');
        Route::put('/{journal_path}/issue/{issue_id}/article/{id}/update', [BackJournalController::class, 'articleUpdate'])->name('article.update');
        Route::delete('/{journal_path}/issue/{issue_id}/article/{id}/destroy', [BackJournalController::class, 'articleDestroy'])->name('article.destroy');
        Route::get('/{journal_path}/issue/{issue_id}/article-export', [BackJournalController::class, 'articleExport'])->name('article.export');
        Route::get('/loa/submission/{id}/generate', [BackJournalController::class, 'loaGenerate'])->name('loa.generate');
        Route::get('/loa/submission/{id}/mail-send', [BackJournalController::class, 'loaMailSend'])->name('loa.mail-send');
        Route::get('/invoice/submission/{id}/generate-1', [BackJournalController::class, 'invoiceGenerate1'])->name('invoice.generate1');
        Route::get('/invoice/submission/{id}/generate-2', [BackJournalController::class, 'invoiceGenerate2'])->name('invoice.generate2');
        Route::get('/invoice/submission/{id}/generate-3', [BackJournalController::class, 'invoiceGenerate3'])->name('invoice.generate3');
        Route::get('/invoice/submission/{id}/mail-send-1', [BackJournalController::class, 'invoiceMailSend1'])->name('invoice.mail-send1');
        Route::get('/invoice/submission/{id}/mail-send-2', [BackJournalController::class, 'invoiceMailSend2'])->name('invoice.mail-send2');
        Route::get('/invoice/submission/{id}/mail-send-3', [BackJournalController::class, 'invoiceMailSend3'])->name('invoice.mail-send3');


        Route::get('/{journal_path}/issue/{issue_id}/editor', [BackJournalController::class, 'editorIndex'])->name('editor.index');
        Route::get('/{journal_path}/issue/{issue_id}/editor/certificate-download/{id?}', [BackJournalController::class, 'editorCertificateDownload'])->name('editor.certificate.download');
        Route::get('/{journal_path}/issue/{issue_id}/editor/certificate-send-mail/{id?}', [BackJournalController::class, 'editorCertificateSendMail'])->name('editor.certificate.send-mail');
        Route::post('/{journal_path}/issue/{issue_id}/editor/file-sk', [BackJournalController::class, 'editorFileSkStore'])->name('editor.file-sk.store');
        Route::get('/{journal_path}/issue/{issue_id}/editor/file-sk-send-mail/{email?}', [BackJournalController::class, 'editorFileSkSendMail'])->name('editor.file-sk.send-mail');
        Route::post('/{journal_path}/issue/{issue_id}/editor/file-fee', [BackJournalController::class, 'editorFileFeeStore'])->name('editor.file-fee.store');
        Route::get('/{journal_path}/issue/{issue_id}/editor/file-fee-send-mail/{email?}', [BackJournalController::class, 'editorFileFeeSendMail'])->name('editor.file-fee.send-mail');
        Route::put('/{journal_path}/issue/{issue_id}/editor/{id}/update', [BackJournalController::class, 'editorUpdate'])->name('editor.update');
        Route::delete('/{journal_path}/issue/{issue_id}/editor/{id}/delete', [BackJournalController::class, 'editorDestroy'])->name('editor.destroy');

        Route::get('/{journal_path}/issue/{issue_id}/reviewer', [BackJournalController::class, 'reviewerIndex'])->name('reviewer.index');
        Route::get('/{journal_path}/issue/{issue_id}/reviewer/export', [BackJournalController::class, 'reviewerExport'])->name('reviewer.export');
        Route::get('/{journal_path}/issue/{issue_id}/reviewer/certificate-download/{id?}', [BackJournalController::class, 'reviewerCertificateDownload'])->name('reviewer.certificate.download');
        Route::get('/{journal_path}/issue/{issue_id}/reviewer/certificate-send-mail/{id?}', [BackJournalController::class, 'reviewerCertificateSendMail'])->name('reviewer.certificate.send-mail');
        Route::post('/{journal_path}/issue/{issue_id}/reviewer/file-sk', [BackJournalController::class, 'reviewerFileSkStore'])->name('reviewer.file-sk.store');
        Route::get('/{journal_path}/issue/{issue_id}/reviewer/file-sk-send-mail/{email?}', [BackJournalController::class, 'reviewerFileSkSendMail'])->name('reviewer.file-sk.send-mail');
        Route::post('/{journal_path}/issue/{issue_id}/reviewer/file-fee', [BackJournalController::class, 'reviewerFileFeeStore'])->name('reviewer.file-fee.store');
        Route::get('/{journal_path}/issue/{issue_id}/reviewer/file-fee-send-mail/{email?}', [BackJournalController::class, 'reviewerFileFeeSendMail'])->name('reviewer.file-fee.send-mail');
        Route::put('/{journal_path}/issue/{issue_id}/reviewer/{id}/update', [BackJournalController::class, 'reviewerUpdate'])->name('reviewer.update');
        Route::delete('/{journal_path}/issue/{issue_id}/reviewer/{id}/delete', [BackJournalController::class, 'reviewerDestroy'])->name('reviewer.destroy');

        Route::get('/{journal_path}/issue/{issue_id}/setting', [BackJournalController::class, 'settingIndex'])->name('setting.index');
    });


    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/verification', [BackFinanceController::class, 'verificationIndex'])->name('verification.index');
        Route::get('/verification/datatable', [BackFinanceController::class, 'verificationDatatable'])->name('verification.datatable');
        Route::get('/verification/{id}/detail', [BackFinanceController::class, 'verificationDetail'])->name('verification.detail');
        Route::put('/verification/{id}/update', [BackFinanceController::class, 'verificationUpdate'])->name('verification.update');
        Route::get('/verification/{id}/delete', [BackFinanceController::class, 'verificationDelete'])->name('verification.delete');
        Route::get('/confirm-payment/{id}/generate', [BackFinanceController::class, 'confirmPaymentGenerate'])->name('confirm-payment.generate');
        Route::get('/confirm-payment/{id}/mail-send', [BackFinanceController::class, 'confirmPaymentMailSend'])->name('confirm-payment.mail-send');

        Route::get('/report', [BackFinanceController::class, 'reportIndex'])->name('report.index');
        Route::get('/report/datatable', [BackFinanceController::class, 'reportDatatable'])->name('report.datatable');
        Route::get('/report/export', [BackFinanceController::class, 'reportExport'])->name('report.export');

        Route::post('cashflow-year/store', [BackFinanceController::class, 'cashflowYearStore'])->name('cashflow-year.store');
        Route::put('cashflow-year/edit', [BackFinanceController::class, 'cashflowYearEdit'])->name('cashflow-year.edit');

        Route::get('/cashflow', [BackFinanceController::class, 'cashflowIndex'])->name('cashflow.index');
        Route::get('/cashflow/datatable', [BackFinanceController::class, 'cashflowDatatables'])->name('cashflow.datatable');
        Route::get('/cashflow/export', [BackFinanceController::class, 'cashflowExport'])->name('cashflow.export');
        Route::post('/cashflow/store', [BackFinanceController::class, 'cashflowStore'])->name('cashflow.store');
        Route::put('/cashflow/{id}/update', [BackFinanceController::class, 'cashflowUpdate'])->name('cashflow.update');
        Route::get('/cashflow/{id}/delete', [BackFinanceController::class, 'cashflowDestroy'])->name('cashflow.destroy');
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

        Route::prefix('reviewer')->name('reviewer.')->group(function () {
            Route::get('/', [BackMasterDataController::class, 'reviewerIndex'])->name('index');
            Route::put('/edit/{reviewer_id}', [BackMasterDataController::class, 'reviewerUpdate'])->name('update');
            Route::get('/export', [BackMasterDataController::class, 'reviewerExport'])->name('export');
            Route::post('/sync-to-user', [BackMasterDataController::class, 'reviewerSyncToUser'])->name('sync-to-user');

        });

        Route::prefix('editor')->name('editor.')->group(function () {
            Route::get('/', [BackMasterDataController::class, 'editorIndex'])->name('index');
            Route::put('/edit/{id}', [BackMasterDataController::class, 'editorUpdate'])->name('update');
            Route::get('/export', [BackMasterDataController::class, 'editorExport'])->name('export');
            Route::post('/sync-to-user', [BackMasterDataController::class, 'editorSyncToUser'])->name('sync-to-user');
        });

        Route::prefix('payment-account')->name('payment-account.')->group(function () {
            Route::get('/', [BackMasterDataController::class, 'paymentAccount'])->name('index');
            Route::put('/update', [BackMasterDataController::class, 'paymentAccountUpdate'])->name('update');
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
        Route::post('/banner/create', [BackSettingController::class, 'bannerCreate'])->name('banner-create');
        Route::put('/banner/{id}/update', [BackSettingController::class, 'bannerUpdate'])->name('banner-update');
        Route::delete('/banner/{id}/delete', [BackSettingController::class, 'bannerDelete'])->name('banner-delete');

        Route::get('/bot', [BackSettingController::class, 'bot'])->name('bot');
        Route::put('/bot', [BackSettingController::class, 'botUpdate'])->name('bot.update');
    });

    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::get('/setting', [App\Http\Controllers\Back\WhatsappController::class, 'setting'])->name('setting');

        Route::prefix('message')->name('message.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('back.whatsapp.message.sendMessage');
            })->name('index');
            Route::get('/send-message', [App\Http\Controllers\Back\WhatsappController::class, 'sendMessage'])->name('sendMessage');
            Route::get('/send-image', [App\Http\Controllers\Back\WhatsappController::class, 'sendImage'])->name('sendImage');
            Route::post('/send-image-process', [App\Http\Controllers\Back\WhatsappController::class, 'sendImageProcess'])->name('sendImageProcess');
            Route::get('/send-bulk-message', [App\Http\Controllers\Back\WhatsappController::class, 'sendBulkMessage'])->name('sendBulkMessage');
            Route::post('/send-bulk-message-process', [App\Http\Controllers\Back\WhatsappController::class, 'sendBulkMessageProcess'])->name('sendBulkMessageProcess');

            Route::post('/send-multi-message-process', [App\Http\Controllers\Back\WhatsappController::class, 'sendMultipleMessageProcess'])->name('sendMultipleMessageProcess');
        });
    });

    Route::prefix('email')->name('email.')->group(function () {
        Route::post('/send-mail', [EmailController::class, 'sendEmail'])->name('send-mail');
        Route::post('/send-multi-mail', [EmailController::class, 'sendEmailMultiple'])->name('send-multi-mail');
    });
});
