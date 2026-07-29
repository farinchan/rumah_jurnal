<?php

use App\Http\Controllers\Back\journalController;

it('adds only the last three invoice sequence digits to the payment amount', function (
    int $invoiceSequence,
    int $expectedAmount
) {
    $method = new ReflectionMethod(journalController::class, 'paymentAmountWithThreeDigitCode');

    expect($method->invoke(new journalController, 600000, $invoiceSequence))
        ->toBe($expectedAmount);
})->with([
    'one digit sequence' => [7, 600007],
    'three digit sequence' => [987, 600987],
    'four digit sequence' => [1234, 600234],
    'last three digits are zero' => [2000, 600000],
]);
