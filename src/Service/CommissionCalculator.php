<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Service;

use DateTimeImmutable;
use Smartvizz\CommissionTask\Service\Rules\CommissionRuleInterface;

class CommissionCalculator
{
    /** @var CommissionRuleInterface[] */
    private $rules;

    /**
     * @param CommissionRuleInterface[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function calculateFee(
        string $dateStr,
        int $userId,
        string $userType,
        string $operationType,
        float $amount,
        string $currency
    ): float {
        $date = $this->parseDate($dateStr);
        foreach ($this->rules as $rule) {
            if ($rule->supports($userType, $operationType)) {
                return $rule->calculate($amount, $currency, $date, $userId);
            }
        }
        return 0.0;
    }

    private function parseDate(string $dateStr): DateTimeImmutable
    {
        // Attempt strict parsing with known format (e.g., "Y-m-d")
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateStr);
        $errors = DateTimeImmutable::getLastErrors();

        if (!$date || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
            throw new \InvalidArgumentException("Invalid date format: {$dateStr}");
        }

        return $date;
    }
}
