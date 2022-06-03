<?php

declare(strict_types=1);

namespace Ruga\Money\PricePart;


/**
 * Interface to a price part.
 *
 * @see      Price
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface PricePartInterface extends \Ruga\Std\Chain\LinkInterface
{
    /**
     * Return the base amount, before this price part is applied.
     *
     * @return \Ruga\Money\Amount
     */
    public function getBaseAmount(): \Ruga\Money\Amount;
    
    
    
    /**
     * Return the absolute amount for this price part. Calculated from given $amount.
     * Calculate the other way (up > down) if $excl is true.
     *
     * @param \Ruga\Money\Amount $amount
     * @param bool               $excl
     *
     * @return \Ruga\Money\Amount
     */
    public function getAbsoluteAmount(): \Ruga\Money\Amount;
    
    
    
    /**
     * Returns the new amount after this price part is applied.
     *
     * @return \Ruga\Money\Amount
     * @todo Move to other interface
     */
    public function getAmount(): \Ruga\Money\Amount;
    
    
    
    /**
     * Returns the desired operation for this price part.
     * Calculate the other way (up > down) if $excl is true.
     *
     * @param bool $excl
     *
     * @return string
     */
    public function getOperation(bool $excl = false): string;
    
    
    
    /**
     * Return the name of this price part.
     *
     * @return string
     */
    public function getName(): string;
    
    
    
    /**
     * Return the value as initially specified.
     *
     * @return string
     */
    public function getValue(): string;
    
    
    
    /**
     * Return the calculation.
     *
     * @return string
     */
    public function getCalculation(): string;
    
    
    
    /**
     * Returns true if this price part is already priced in.
     *
     * @return bool
     */
    public function isPricedIn(): bool;
    
    
    
    /**
     * Defines, where the resulting absolute amount should be placed.
     *
     * @param PricePartableInterface $output
     *
     * @return PricePartInterface
     */
    public function output(PricePartableInterface $output): PricePartInterface;
    
}
