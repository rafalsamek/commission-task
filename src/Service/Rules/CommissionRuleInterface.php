<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Service\Rules;

use DateTimeImmutable;

interface CommissionRuleInterface
{
    public function supports(string $userType, string $operationType): bool;

    public function calculate(
        float $amount,
        string $currency,
        DateTimeImmutable $date,
        int $userId
    ): float;
}
