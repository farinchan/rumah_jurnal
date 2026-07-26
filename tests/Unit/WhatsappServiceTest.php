<?php

use App\Services\WhatsappService;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

it('keeps an explicitly supplied international WhatsApp country code', function () {
    Http::fake([
        '*' => Http::response([], 200),
    ]);

    (new WhatsappService)->sendMessage('+1 415 555 0123', 'Test message');

    Http::assertSent(fn ($request) => $request['chatId'] === '14155550123');
});

it('converts a local Indonesian WhatsApp number to country code 62', function () {
    Http::fake([
        '*' => Http::response([], 200),
    ]);

    (new WhatsappService)->sendMessage('0812-3456-7890', 'Test message');

    Http::assertSent(fn ($request) => $request['chatId'] === '6281234567890');
});
