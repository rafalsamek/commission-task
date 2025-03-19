<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use Smartvizz\CommissionTask\Service\CommissionCalculator;
use Smartvizz\CommissionTask\Service\CurrencyConversionService;
use Smartvizz\CommissionTask\Service\Rules\BusinessWithdrawRule;
use Smartvizz\CommissionTask\Service\Rules\DepositRule;
use Smartvizz\CommissionTask\Service\Rules\PrivateWithdrawRule;

final class CommissionCalculatorTest extends TestCase
{
    /** @var CommissionCalculator */
    private $calculator;

    protected function setUp()
    {
        $this->calculator = new CommissionCalculator(
            [
                new DepositRule(),
                new BusinessWithdrawRule(),
                new PrivateWithdrawRule(
                    new CurrencyConversionService()
                ),
            ]
        );
    }

    public function testDepositFee()
    {
        // e.g. deposit 200.0 EUR => 200 * 0.03% => 0.06
        $fee = $this->calculator->calculateFee(
            '2025-05-01',
            7,
            'private',
            'deposit',
            200.0,
            'EUR'
        );
        $this->assertEquals(0.06, $fee, '', 0.000001);
    }

    public function testBusinessWithdraw()
    {
        // 300 EUR => 0.5% => 1.50
        $fee = $this->calculator->calculateFee(
            '2025-05-02',
            2,
            'business',
            'withdraw',
            300.0,
            'EUR'
        );
        $this->assertEquals(1.50, $fee, '', 0.000001);
    }

    public function testPrivateWithinFreeLimit()
    {
        // user=4, 1st private withdraw of the week, 500.0 EUR => no exceed => 0.0 fee
        $fee = $this->calculator->calculateFee(
            '2025-05-03',
            4,
            'private',
            'withdraw',
            500.0,
            'EUR'
        );
        $this->assertEquals(0.0, $fee);
    }
}
