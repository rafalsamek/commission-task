<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Service;

final class FeeFormatter
{
    public static function formatFee(float $fee, string $currency): string
    {
        $decimals = 2;
        if ($currency === 'JPY') {
            $decimals = 0;
        }

        // Round up if needed (assuming you've done so in logic, this might be optional)
        $factor = 10 ** $decimals;
        $fee = ceil($fee * $factor) / $factor;

        return number_format($fee, $decimals, '.', '');
    }
}
