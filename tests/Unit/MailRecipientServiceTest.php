<?php

use App\Services\MailRecipientService;

uses(Tests\TestCase::class);

it('uses the actual recipient in production', function () {
    config([
        'mail.environment' => 'production',
        'mail.local_address' => 'local@example.com',
    ]);

    expect(app(MailRecipientService::class)->resolve('author@example.com'))
        ->toBe('author@example.com');
});

it('uses the local recipient outside production', function () {
    config([
        'mail.environment' => 'local',
        'mail.local_address' => 'local@example.com',
    ]);

    expect(app(MailRecipientService::class)->resolve('author@example.com'))
        ->toBe('local@example.com');
});

it('fails safely when the local recipient is not configured', function () {
    config([
        'mail.environment' => 'local',
        'mail.local_address' => null,
    ]);

    app(MailRecipientService::class)->resolve('author@example.com');
})->throws(RuntimeException::class);
