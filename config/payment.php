<?php

return [
    'bank_name' => env('DOCUMENT_PAYMENT_BANK_NAME', 'Al-Kuraimi Bank'),
    'account_name' => env('DOCUMENT_PAYMENT_ACCOUNT_NAME', 'Saba Region University'),
    'account_number' => env('DOCUMENT_PAYMENT_ACCOUNT_NUMBER', ''),
    'instructions' => env('DOCUMENT_PAYMENT_INSTRUCTIONS', 'Please include the request tracking code in the transfer note.'),
];
