<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Money;

/**
 * Stores a price for a certain good. Actually this is just a container for an amount, but a price can be placed
 * in a position and prices can be connected.
 * Price is IMMUTABLE.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class Price implements PricePart\PricePartableInterface, Basket\DumpableInterface, \Ruga\Std\Chain\AnchorInterface
{
    use PricePart\PricePartableTrait;
    use \Ruga\Std\Chain\LinkTrait;
    
    
    /**
     * Store for the price.
     *
     * @var Amount
     */
    private $price = null;
    
    
    
    /**
     * Initialize the class and store the initial (immutable) price.
     *
     * @param string|int|float|Amount $amount
     */
    public function __construct($amount, string $currency = null)
    {
        $this->price = new Amount($amount, $currency);
    }
    
    
    
    public function dump(): array
    {
        return [
            'type' => self::class,
            'total_excl' => "{$this->excl()}",
            'total' => "{$this->getAmount()}",
            'total_incl' => "{$this->incl()}",
            'includes' => $this->dump_includes(),
            'excludes' => $this->dump_excludes(),
        ];
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartableInterface::getAmount()
     */
    public function getAmount(): \Ruga\Money\Amount
    {
// 		echo get_called_class() . "::getAmount()" . PHP_EOL;
        return $this->price;
    }
    
    
    
    /**
     * Return the amount as string for output.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getAmount()->__toString();
    }
}
