<?php

declare(strict_types=1);

namespace Ruga\Money;

use Ruga\Money\Exception\CurrencyMismatchException;
use Ruga\Money\Exception\CurrencyNotSetException;

/**
 * Stores an (immutable) amount of money and allows calculations.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class Amount
{
    /**
     * Store for the actual amount as arbitrary precision number
     *
     * @var string
     */
    private string $raw_amount = '0';
    
    
    /**
     * Number of digits after the decimal place.
     * Currently we use DECIMAL(19,4) as DB field.
     *
     * @todo Depend on Currency?
     * @var integer
     */
    private int $scale = 4;
    
    
    /**
     * Currency code as 3-letter string.
     *
     * @var string|NULL
     */
    private ?string $ccy = null;
    
    
    /**
     * Show currency symbol?
     *
     * @var bool
     */
    private bool $showSymbol = true;
    
    
    /**
     * Used for number formatter.
     *
     * @var string
     */
    private string $locale = 'de_CH';
    
    
    
    /**
     * Initialize the class and store the initial (immutable) amount.
     *
     * @param string|int|float|self $amount
     * @param string|null           $currency
     * @param array|null            $options
     */
    public function __construct($amount, string $currency = null, array $options = [])
    {
        if ($amount instanceof Amount) {
            $currency = $amount->getCurrency();
            $options = $amount->getOptions();
            $amount = $amount->getAmountRaw();
        }
        
        $this->setOptions($options);
        
        if ($currency !== null) {
            $iso3166 = new \Ruga\I18n\Iso3166(
                (new \Ruga\I18n\Iso3166\Filter\All())
                    ->then(new \Ruga\I18n\Iso3166\Filter\ChangeKey(\Ruga\I18n\Iso3166\Key::CURRENCY_CODE))
            );
            
            $this->ccy = $iso3166->getString($currency, \Ruga\I18n\Iso3166\Key::CURRENCY_CODE);
        }
        
        $string_amount = (string)$amount;
        $this->raw_amount = bcadd($this->raw_amount, $string_amount, $this->scale);
    }
    
    
    
    /**
     * Create a new object and set the amount to the specified value. This replicates all the necessary
     * properties to the new object, like currency, locale, ...
     *
     * @param string $amount
     * @param array  $options
     *
     * @return \Ruga\Money\Amount
     */
    private function clone(string $amount, array $options = []): self
    {
        return new self(
            $amount, $this->ccy, array_merge($this->getOptions(), $options)
        );
    }
    
    
    
    /**
     * Return the options as array.
     *
     * @return array
     */
    private function getOptions(): array
    {
        return [
            'locale' => $this->locale,
            'showSymbol' => $this->showSymbol,
            'scale' => $this->scale,
        ];
    }
    
    
    
    /**
     * Set options as array.
     *
     * @param array $options
     *
     * @return void
     */
    private function setOptions(array $options): void
    {
        if (isset($options['scale'])) {
            $this->scale = $options['scale'];
        }
        if (isset($options['showSymbol'])) {
            $this->showSymbol = $options['showSymbol'];
        }
        if (isset($options['locale'])) {
            $this->locale = $options['locale'];
        }
    }
    
    
    
    /**
     * Compare $amount to the stored amount and return the result.
     * -1: $amount is larger ($this < $amount)
     *  0: Equal ($this == $amount)
     *  1: this amount is larger ($this > $amount)
     *
     * @param self|int|float|string $amount
     *
     * @return int
     * @throws \Exception
     */
    public function comp($amount): int
    {
        $amount = new Amount($amount);
        $this->checkOperand($amount, 'comp');
        return bccomp($this->getAmountRaw(), $amount->getAmountRaw(), $this->scale);
    }
    
    
    
    /**
     * Add $amount to the stored amount and return a new Amount object.
     *
     * @param self|int|float|string $amount
     *
     * @return self
     * @throws \Exception
     */
    public function add($amount): self
    {
        $amount = new Amount($amount);
        $this->checkOperand($amount, 'add');
        return $this->clone(bcadd($this->getAmountRaw(), $amount->getAmountRaw(), $this->scale));
    }
    
    
    
    /**
     * Subtract $amount from the stored amount and return a new Amount object.
     *
     * @param self|int|float|string $amount
     *
     * @return self
     * @throws \Exception
     */
    public function sub($amount): self
    {
        $amount = new Amount($amount);
        $this->checkOperand($amount, 'sub');
        return $this->clone(bcsub($this->getAmountRaw(), $amount->getAmountRaw(), $this->scale));
    }
    
    
    
    /**
     * Multiply the stored amount with $amount and return a new Amount object.
     *
     * @param string|int|float $right_operand
     *
     * @return self
     */
    public function mul($right_operand): self
    {
        $right_operand = (string)$right_operand;
        return $this->clone(bcmul($this->getAmountRaw(), $right_operand, $this->scale));
    }
    
    
    
    /**
     * Divide the stored amount by $amount and return a new Amount object.
     *
     * @param string|int|float $right_operand
     *
     * @return self
     */
    public function div($divisor): self
    {
        $divisor = (string)$divisor;
        return $this->clone(bcdiv($this->getAmountRaw(), $divisor, $this->scale));
    }
    
    
    
    /**
     * Converts the currency to $newcurrency and returns a new Amount object.
     *
     * @param string           $newcurrency
     * @param string|float|int $rate
     * @param int|null         $scale
     *
     * @return self
     */
    public function convertTo(string $newcurrency, $rate, ?int $scale = null): self
    {
        $rate = (string)$rate;
        if ($scale === null) {
            $scale = $this->getOptions()['scale'];
        }
        return new self(bcmul($this->getAmountRaw(), $rate, $scale), $newcurrency, ['scale' => $scale]);
    }
    
    
    
    /**
     * Checks if supplied $operand can be used to execute the operation.
     *
     * @param self   $operand
     * @param string $operation
     *
     * @return bool
     * @throws \Exception
     */
    private function checkOperand(self $operand, $operation = '')
    {
        if ($operand->getCurrency() !== $this->getCurrency()) {
            throw new CurrencyMismatchException(
                "Operation {$operation} not possible with different currencies ({$this->getCurrency()}/{$operand->getCurrency()})."
            );
        }
        return true;
    }
    
    
    
    /**
     * Returns the currency code or null if no currency is set.
     *
     * @return string|NULL
     */
    public function getCurrency(): ?string
    {
        return $this->ccy;
    }
    
    
    
    /**
     * Return the raw amount as stored in the object.
     *
     * @return string
     */
    public function getAmountRaw(): string
    {
        return $this->raw_amount;
    }
    
    
    
    /**
     * Show or hide currency symbol in self::__toString().
     *
     * @param bool $showSymbol
     *
     * @return bool
     */
    public function showSymbol(bool $showSymbol = null): bool
    {
        if ($showSymbol !== null) {
            $this->showSymbol = $showSymbol;
        }
        return $this->showSymbol;
    }
    
    
    
    /**
     * Creates an instance of \NumberFormatter for the current object.
     *
     * @param bool     $withCcySymbol
     * @param int|null $fraction_digits
     *
     * @return \NumberFormatter
     */
    private function getCurrencyFormatter(bool $withCcySymbol = true, int $fraction_digits = null): \NumberFormatter
    {
        $f = new \NumberFormatter($this->locale, \NumberFormatter::CURRENCY);
        
        // Set Currency
        if ($this->getCurrency()) {
            $f->setTextAttribute(\NumberFormatter::CURRENCY_CODE, $this->getCurrency());
        }
        
        // Set fraction digits
        if ($fraction_digits !== null) {
            $f->setAttribute(\NumberFormatter::FRACTION_DIGITS, $fraction_digits);
        }
        
        // Remove currency symbol
        if (!$withCcySymbol) {
            $f->setPattern(trim(str_replace(["¤", "\xC2\xA0"], '', $f->getPattern())));
        }
        
        // If CLI, use normal space instead of NO-BREAK SPACE
        if (php_sapi_name() == "cli") {
            $f->setPattern(str_replace("\xC2\xA0", ' ', $f->getPattern()));
        }
        
        return $f;
    }
    
    
    
    /**
     * Returns the raw value. Represented as a PHP parseable "float string".
     * NumberFormatter::MIN_FRACTION_DIGITS is set to precision value.
     *
     * @param int $fraction_digits Number of digits shown (rounded)
     *
     * @return string
     */
    public function amount(int $fraction_digits = 100): string
    {
        $f = $this->getCurrencyFormatter(false, $fraction_digits);
        
        // Show the full precision we have, instead of the currency default
        $f->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $this->scale);
        
        // Don't group
        $f->setAttribute(\NumberFormatter::GROUPING_USED, false);
        
        return $f->format($this->getAmountRaw());
    }
    
    
    
    /**
     * Returns the raw value. Represented as a localized, formatted string.
     * NumberFormatter::MIN_FRACTION_DIGITS is set to precision value.
     *
     * @param int $fraction_digits Number of digits shown (rounded)
     *
     * @return string
     */
    public function formatAmount(int $fraction_digits = 100): string
    {
        $f = $this->getCurrencyFormatter(false, $fraction_digits);
        
        // Show the full precision we have, instead of the currency default
        $f->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $this->scale);
        
        return $f->format($this->getAmountRaw());
    }
    
    
    
    /**
     * Returns the rounded value. Represented as a currency string, including
     * currency symbol.
     *
     * @return string
     */
    public function formatCurrency(): string
    {
        if (!$this->getCurrency()) {
            throw new CurrencyNotSetException();
        }
        
        $f = $this->getCurrencyFormatter();
        
        // Show the full precision we have, instead of the currency default
        $f->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $this->scale);
        
        return $f->formatCurrency((float)$this->getAmountRaw(), $this->getCurrency());
    }
    
    
    
    /**
     * Returns the rounded value as new Amount object.
     *
     * @return self
     */
    public function rounded(): self
    {
        $f = $this->getCurrencyFormatter(false);
        
        // Don't group
        $f->setAttribute(\NumberFormatter::GROUPING_USED, false);
        
        return $this->clone(
            $f->formatCurrency((float)$this->getAmountRaw(), $this->getCurrency()),
            ['scale' => $f->getAttribute(\NumberFormatter::MIN_FRACTION_DIGITS)]
        );
    }
    
    
    
    /**
     * Returns the rounding difference between the number returned by
     * self::rounded() and self::amount().
     *
     * @return Amount
     * @throws \Exception
     */
    public function roundingDiff(): Amount
    {
        return $this->clone($this->rounded()->amount())->sub($this);
    }
    
    
    
    /**
     * Return the amount as string for output. Tries to print the amount in a locale- and currency-correct way.
     * Can not be used for further calculation because of the currency symbol.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->getCurrency()) {
            return $this->showSymbol() ? $this->rounded()->formatCurrency() : $this->rounded()->formatAmount();
        } else {
            return $this->formatAmount();
        }
    }
    
    
}
