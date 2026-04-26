<?php
return [
    'name' => 'Expenses',
    'table_prefix' => 'exp_',
    'auto_approval_threshold' => env('EXPENSES_AUTO_APPROVAL_THRESHOLD', 100),
    'receipt_required_threshold' => env('EXPENSES_RECEIPT_THRESHOLD', 25),
];
