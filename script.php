#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Smartvizz\CommissionTask\Service\CommissionCalculator;
use Smartvizz\CommissionTask\Service\CurrencyConversionService;
use Smartvizz\CommissionTask\Service\FeeFormatter;
use Smartvizz\CommissionTask\Service\Rules\BusinessWithdrawRule;
use Smartvizz\CommissionTask\Service\Rules\DepositRule;
use Smartvizz\CommissionTask\Service\Rules\PrivateWithdrawRule;

if ($argc < 2) {
    fwrite(STDERR, "Usage: php script.php input.csv\n");
    exit(1);
}

$filePath = $argv[1];
if (!file_exists($filePath)) {
    fwrite(STDERR, "File not found: $filePath\n");
    exit(1);
}

$calculator = new CommissionCalculator([
    new DepositRule(),
    new BusinessWithdrawRule(),
    new PrivateWithdrawRule(
        new CurrencyConversionService()
    ),
]);
$handle = fopen($filePath, 'r');
if (!$handle) {
    fwrite(STDERR, "Cannot open file: $filePath\n");
    exit(1);
}

while (($row = fgetcsv($handle, 1000, ',')) !== false) {
    if (count($row) < 6) {
        continue;
    }
    [$date, $userId, $userType, $operationType, $amount, $currency] = $row;
    $fee = $calculator->calculateFee(
        trim($date),
        (int)$userId,
        trim($userType),
        trim($operationType),
        (float)$amount,
        trim($currency)
    );
    echo FeeFormatter::formatFee($fee, $currency), PHP_EOL;
}

fclose($handle);
