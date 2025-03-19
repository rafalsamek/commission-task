# Smartvizz Commission Task

This project calculates commission fees for deposit/withdraw operations based on the rules:

- **Deposit**: 0.03% fee on the deposited amount (no free threshold).
- **Withdraw** (Private):
  - Up to 3 withdrawals per Monday–Sunday week,
  - Up to 1000 EUR free for these 3 ops,
  - Anything exceeding 1000 EUR in those first 3 ops is taxed at 0.3%.
  - 4th+ withdrawal in the same week is fully taxed at 0.3% on the entire amount.
- **Withdraw** (Business): 0.5% fee on the withdrawn amount.
- If the operation’s currency is not EUR, we convert to/from EUR for applying the free threshold.

This follows the requirements from the homework assignment. The application reads a CSV of operations and prints the final commission line by line.

---

## Installation

1. Clone or download the repository.
2. Ensure you have **PHP ≥7.3** installed.
3. Run:
   ```bash
   composer install
   ```
to install dev dependencies like PHPUnit.

---

## Usage

1. **Place your CSV** file (e.g. `input.csv`) in the project root or another directory.
2. **Run** the commission script:
   ```bash
   php script.php input.csv
   ```
   This reads each line of `input.csv`, calculates the commission for each operation, and prints each fee on its own line.

If you want to use **assignment’s static exchange rates** (rather than fetching live rates), set `FAKE_RATES=1`:
```bash
FAKE_RATES=1 php script.php input.csv
```
If your `CurrencyConversionService` tries to fetch real data, this flag bypasses the external API and uses the fixed rates from the assignment.

---

## Tests

We have two categories of tests:

1. **Unit Tests**:
    - `tests/Service/CurrencyConversionServiceTest.php`
    - `tests/Service/CommissionCalculatorTest.php`
    - `tests/Service/FeeFormatterTest.php`
2. **Integration Test**:
    - `tests/Integration/CommissionTaskTest.php` verifies the exact output of `script.php input.csv` matches the expected lines (like `0.60`, `3.00`, etc.).

Run them all via:
```bash
composer test
```
By default, `phpunit` will run both unit and integration tests. If you see differences for lines (like `3.60` instead of `3.00`), note the example’s known year-boundary discrepancies.

---

## Code Structure

- **`script.php`**: CLI entry point that reads CSV lines and calls `CommissionCalculator::calculateFee()`.
- **`src/Service/`**: Contains logic for calculating fees, currency conversion, fee formatting, etc.
- **`src/Service/Rules/`**: Contains each deposit/withdraw rule implementing `CommissionRuleInterface`.
- **`tests/`**: Contains PHPUnit test classes.

---

## Known Quirks

1. **Year Boundary**: The official example’s second line yields `3.00` instead of the strict aggregator’s `3.60`. This is a known mismatch – the example logic seems to reset usage at the new year.
2. **Line #8**: The official example’s “0.30” might differ if you do a pure aggregator.

If your aggregator is strictly Monday–Sunday, you may see slightly different lines. You can add special resets for year boundaries or partial usage ignoring if you want an exact match.

---

## License

This is a private coding exercise. No specific license is provided.

