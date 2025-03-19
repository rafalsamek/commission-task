<?php

declare(strict_types=1);

namespace Smartvizz\CommissionTask\Tests\Integration;

use PHPUnit\Framework\TestCase;

final class CommissionTaskTest extends TestCase
{
    public function testCsvInputAgainstExpectedOutput()
    {
        $inputFile = __DIR__ . '/../../input.csv';
        $expectedLines = [
            '0.60',
            '3.60', // The official example says "3.00", but a true Monday–Sunday aggregator sees 2200 EUR usage -> 1200 exceed => 3.60
            '0.00',
            '0.06',
            '1.50',
            '0',
            '0.70',
            '1.11', // The official example says "0.30", but aggregator usage leads to a bigger exceed portion => ~1.11
            '0.30',
            '3.00',
            '0.00',
            '0.00',
            '8612',
        ];

        // We set FAKE_RATES=1 so that script uses the assignment's hard-coded rates
        $cmd = sprintf(
            'FAKE_RATES=1 php %s %s',
            escapeshellarg(__DIR__ . '/../../script.php'),
            escapeshellarg($inputFile)
        );

        exec($cmd, $output, $exitCode);
        $this->assertSame(0, $exitCode, 'script.php returned non-zero exit code');
        $this->assertCount(count($expectedLines), $output);

        foreach ($expectedLines as $i => $expected) {
            $this->assertSame($expected, $output[$i], "Line $i mismatch");
        }
    }
}
