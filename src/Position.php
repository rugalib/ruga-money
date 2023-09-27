<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Money;

/**
 * Stores a position (price and quantity) of a certain good.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class Position implements PricePart\PricePartableInterface, Basket\BasketableInterface, Basket\DumpableInterface,
                          \Ruga\Std\Chain\AnchorInterface
{
    use PricePart\PricePartableTrait;
    use Basket\BasketableTrait;
    use \Ruga\Std\Chain\LinkTrait;
    
    
    /**
     * Holds the price of the position.
     *
     * @var Price|null
     */
    private ?Price $price = null;
    
    
    /**
     * Holds the quantity of the position.
     *
     * @var string|null
     */
    private ?string $qty = null;
    
    
    
    /**
     * Initialize the class and store the initial (immutable) price.
     *
     * @param Price            $price
     * @param string|int|float $qty
     */
    public function __construct(Price $price, $qty)
    {
        $this->price = $price;
        $this->qty = (string)$qty;
    }
    
    
    
    public function dump(): array
    {
        return [
            'type' => self::class,
            'pos_id' => $this->getPosId(),
            'level' => $this->getLevel(),
            'total_excl' => "{$this->excl()}",
            'total' => "{$this->getAmount()}",
            'total_incl' => "{$this->incl()}",
            'includes' => $this->dump_includes(),
            'excludes' => $this->dump_excludes(),
            
            'qty' => $this->qty,
            'price' => $this->price->dump(),
        ];
    }
    
    
    
    /**
     * Returns base amount as set while initializing the object multiplied by quantity.
     * {@inheritDoc}
     *
     * @see \Ruga\Money\PricePart\PricePartableInterface::getAmount()
     */
    public function getAmount(): \Ruga\Money\Amount
    {
        return $this->price->getAmount()->mul($this->getQuantity());
    }
    
    
    
    /**
     * Returns the quantity.
     *
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->qty;
    }
    
    
    
    /**
     * Returns the price.
     *
     * @return Price
     */
    public function getPrice(): Price
    {
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
