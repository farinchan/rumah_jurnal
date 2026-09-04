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

it('resolves multiple unique valid recipients in production', function () {
    config([
        'mail.environment' => 'production',
        'mail.local_address' => 'local@example.com',
    ]);

    expect(app(MailRecipientService::class)->resolveMany([
        'author1@example.com',
        'author2@example.com',
        'author1@example.com',
        'invalid-email',
    ]))->toBe([
        'author1@example.com',
        'author2@example.com',
    ]);
});

it('resolves multiple recipients to single local address outside production', function () {
    config([
        'mail.environment' => 'local',
        'mail.local_address' => 'local@example.com',
    ]);

    expect(app(MailRecipientService::class)->resolveMany([
        'author1@example.com',
        'author2@example.com',
    ]))->toBe([
        'local@example.com',
    ]);
});
