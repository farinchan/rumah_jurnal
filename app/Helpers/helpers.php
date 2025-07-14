<?php

use Carbon\Carbon;

/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('whatsappNumber')) {
    function whatsappNumber($number)
    {
        $phone = preg_replace('/\D/', '', $number);

        // Jika nomor diawali "08", ubah menjadi "628"
        if (substr($phone, 0, 2) === "08") {
            return "628" . substr($phone, 2);
        }

        return $phone;
    }
}
