<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Money\PricePart;


/**
 * Interface to a price part.
 *
 * @see      Price
 * @see      AbstractPricePart
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface PricePartInterface extends \Ruga\Std\Chain\LinkInterface
{
    /**
     * Return the base amount, before this price part is applied. For included price parts, this is the amount
     * before the reverse calculation is applied.
     *
     * @return \Ruga\Money\Amount
     */
    public function getBaseAmount(): \Ruga\Money\Amount;
    
    
    
    /**
     * Return the absolute amount for this price part.
     *
     * @return \Ruga\Money\Amount
     */
    public function getAbsoluteAmount(): \Ruga\Money\Amount;
    
    
    
    /**
     * Returns the amount after this price part is applied. For included price parts, this is the amount after
     * applying the reversed calculation.
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
     * Return the calculation as string. This is used for explanation of the price part and how the price is
     * created.
     *
     * @return string
     */
    public function getCalculation(): string;
    
    
    
    /**
     * Returns true if this price part is already priced in.
     *
     * @return bool
     * @deprecated
     */
    public function isPricedIn(): bool;
    
    
    
    /**
     * Returns true, if the price part is an included price part.
     *
     * @return bool
     */
    public function isIncl(): bool;
    
    
    
    /**
     * Returns true, if the price part is an excluded price part.
     *
     * @return bool
     */
    public function isExcl(): bool;
    
    
    /**
     * Defines, where the resulting absolute amount should be placed. This allows you to put VAT amounts in a seperated
     * position in your basket.
     *
     * @param PricePartableInterface $output
     *
     * @return PricePartInterface
     */
    public function output(PricePartableInterface $output): PricePartInterface;
    
}
