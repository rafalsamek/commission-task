<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use Smartvizz\CommissionTask\Service\FeeFormatter;

final class FeeFormatterTest extends TestCase
{
    public function testFormatFeeEurZero()
    {
        $this->assertSame(
            '0.00',
            FeeFormatter::formatFee(0.0, 'EUR')
        );
    }

    public function testFormatFeeEurNonInteger()
    {
        // Suppose we want "3.60" if it's 3.60
        $this->assertSame(
            '3.60',
            FeeFormatter::formatFee(3.599, 'EUR')
        );
    }

    public function testFormatFeeJpy()
    {
        // JPY => 0 decimals
        $this->assertSame(
            '4',
            FeeFormatter::formatFee(3.5, 'JPY')
        );
    }
}
