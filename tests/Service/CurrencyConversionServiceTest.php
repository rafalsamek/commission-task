<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Smartvizz\CommissionTask\Service\CurrencyConversionService;

final class CurrencyConversionServiceTest extends TestCase
{
    /** @var CurrencyConversionService */
    private $service;

    protected function setUp()
    {
        putenv('FAKE_RATES=1');
        $this->service = new CurrencyConversionService();
    }

    public function testToEurWithKnownRates()
    {
        // Suppose "USD" => 1.1497
        // 11.497 USD => 10.0 EUR
        $this->assertEquals(
            10.0,
            $this->service->toEur(11.497, 'USD'),
            '',
            0.0001
        );
    }

    public function testFromEurWithKnownRates()
    {
        // 10.0 EUR => 10 * 1.1497 => 11.497 USD
        $this->assertEquals(
            11.497,
            $this->service->fromEur(10.0, 'USD'),
            '',
            0.0001
        );
    }

    public function testUnknownCurrencyThrowsOrReturnsSame()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown currency: XYZ');

        $this->service->toEur(50.0, 'XYZ');
    }
}
