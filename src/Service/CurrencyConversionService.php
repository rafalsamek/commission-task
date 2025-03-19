<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Service;

use RuntimeException;

class CurrencyConversionService
{
    /** @var string */
    private $apiUrl = 'https://api.exchangeratesapi.io/latest?access_key=e7d5611a5751c8ebb196f781bfddc236';

    /** @var array<string, float> */
    private $rates = [];

    public function __construct()
    {
        $this->updateRatesFromApi();
    }

    public function toEur(float $amount, string $currency): float
    {
        if (!isset($this->rates[$currency])) {
            throw new RuntimeException("Unknown currency: {$currency}");
        }
        return $amount / $this->rates[$currency];
    }

    public function fromEur(float $amountEur, string $currency): float
    {
        if (!isset($this->rates[$currency])) {
            throw new RuntimeException("Unknown currency: {$currency}");
        }
        return $amountEur * $this->rates[$currency];
    }

    private function updateRatesFromApi(): void
    {
        // If 'FAKE_RATES' is set to '1', use the assignment's example rates
        if (getenv('FAKE_RATES') === '1') {
            $this->rates = [
                'EUR' => 1.0,
                'USD' => 1.1497,
                'JPY' => 129.53
            ];
            return;
        }

        // Otherwise, fetch real rates
        $json = @file_get_contents($this->apiUrl);
        if ($json === false) {
            throw new RuntimeException(
                "Failed to fetch currency rates from {$this->apiUrl}"
            );
        }

        $data = @json_decode($json, true);
        if (!isset($data['rates']) || !is_array($data['rates'])) {
            throw new RuntimeException(
                "Invalid data structure returned by {$this->apiUrl}"
            );
        }

        $this->rates = $data['rates'];
        if (!isset($this->rates['EUR']) || !is_numeric($this->rates['EUR'])) {
            // The service is supposed to have EUR as base=1.0,
            // but if it's missing or invalid, throw:
            throw new RuntimeException(
                "Missing 'EUR' rate in data from {$this->apiUrl}"
            );
        }
    }
}
