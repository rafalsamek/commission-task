<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Service\Rules;

use DateTimeImmutable;

final class BusinessWithdrawRule implements CommissionRuleInterface
{
    public function supports(string $userType, string $operationType): bool
    {
        return $userType === 'business' && $operationType === 'withdraw';
    }

    public function calculate(
        float $amount,
        string $currency,
        DateTimeImmutable $date,
        int $userId
    ): float {
        $fee = $amount * (0.5 / 100); // 0.5% for business withdrawals
        return $this->roundUp($fee, $currency);
    }

    private function roundUp(float $fee, string $currency): float
    {
        $decimals = ($currency === 'JPY') ? 0 : 2;
        $factor = 10 ** $decimals;
        return ceil($fee * $factor) / $factor;
    }
}
