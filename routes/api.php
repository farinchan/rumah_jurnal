<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/journal/store', [App\Http\Controllers\Api\JournalController::class, 'journalStore'])->name('journal.store');
    Route::post('/journal/sync', [App\Http\Controllers\Api\JournalController::class, 'journalSync'])->name('journal.sync');

    Route::get('/submissions/list', [App\Http\Controllers\Api\JournalController::class, 'submissionsList'])->name('submissions.list');
    Route::post('/submissions/select', [App\Http\Controllers\Api\JournalController::class, 'submissionsSelect'])->name('submissions.select');

    Route::get('/reviewer/list', [App\Http\Controllers\Api\JournalController::class, 'reviewerList'])->name('reviewer.list');
    Route::post('/reviewer/select', [App\Http\Controllers\Api\JournalController::class, 'reviewerSelect'])->name('reviewer.select');

    Route::get('/editor/list', [App\Http\Controllers\Api\JournalController::class, 'editorList'])->name('editor.list');
    Route::post('/editor/select', [App\Http\Controllers\Api\JournalController::class, 'editorSelect'])->name('editor.select');

    Route::get('/journal/list', [App\Http\Controllers\Api\JournalController::class, 'journalList'])->name('journal.list');
    Route::post('/journal/get/{context_id}', [App\Http\Controllers\Api\JournalController::class, 'journalGet'])->name('journal.get');


    Route::get('/data/website', [App\Http\Controllers\Api\DataController::class, 'datawebsite'])->name('data.website');
    Route::get('/data/banner', [App\Http\Controllers\Api\DataController::class, 'dataBanner'])->name('data.banner');

    Route::get('/data/journal', [App\Http\Controllers\Api\DataController::class, 'dataJournal'])->name('data.journal');
    Route::get('/data/journal/{context_id}', [App\Http\Controllers\Api\DataController::class, 'dataJournalContext'])->name('data.journal.context');

});
