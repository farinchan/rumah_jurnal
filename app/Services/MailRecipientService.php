<?php

namespace App\Services;

use RuntimeException;

class MailRecipientService
{
    public function resolve(string $productionAddress): string
    {
        if (config('mail.environment', 'local') === 'production') {
            return $productionAddress;
        }

        $localAddress = config('mail.local_address');

        if (! is_string($localAddress) || ! filter_var($localAddress, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException(
                'MAIL_LOCAL_ADDRESS must contain a valid email address when MAIL_ENVIRONMENT is not production.'
            );
        }

        return $localAddress;
    }
}
