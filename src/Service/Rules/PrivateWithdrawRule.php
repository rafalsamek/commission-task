<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Service\Rules;

use DateTimeImmutable;
use Smartvizz\CommissionTask\Service\CurrencyConversionService;

class PrivateWithdrawRule implements CommissionRuleInterface
{
    /** @var array */
    private static $weeklyUsage = [];

    /** @var CurrencyConversionService */
    private $conversion;

    public function __construct(CurrencyConversionService $conversion)
    {
        $this->conversion = $conversion;
    }

    public function supports(string $userType, string $operationType): bool
    {
        return $userType === 'private' && $operationType === 'withdraw';
    }

    public function calculate(
        float $amount,
        string $currency,
        DateTimeImmutable $date,
        int $userId
    ): float {
        $yearWeek = $date->format('o-\WW');
        if (!isset(self::$weeklyUsage[$userId][$yearWeek])) {
            self::$weeklyUsage[$userId][$yearWeek] = ['count' => 0, 'totalEur' => 0.0];
        }

        $usage   = self::$weeklyUsage[$userId][$yearWeek];
        $count   = $usage['count'];
        $usedEur = $usage['totalEur'];

        $amountEur = $this->conversion->toEur($amount, $currency);
        $count++;
        $usedEur += $amountEur;

        $fee = 0.0;
        if ($count > 3) {
            $fee = $amount * (0.3 / 100);
        } else {
            $exceedEur = $usedEur - 1000.0;
            if ($exceedEur > 0) {
                $exceedOrig = $this->conversion->fromEur($exceedEur, $currency);
                $fee = $exceedOrig * (0.3 / 100);
            }
        }

        self::$weeklyUsage[$userId][$yearWeek] = [
            'count'    => $count,
            'totalEur' => $usedEur,
        ];

        return $this->roundUp($fee, $currency);
    }

    private function roundUp(float $fee, string $currency): float
    {
        $decimals = ($currency === 'JPY') ? 0 : 2;
        $factor = 10 ** $decimals;
        return ceil($fee * $factor) / $factor;
    }
}
