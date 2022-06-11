<?php

declare(strict_types=1);

namespace Ruga\Money\PricePart;


/**
 * Interface to a object that can use price parts.
 *
 * @see      Price
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface PricePartableInterface
{
    /**
     * Returns base amount as set while initializing the object.
     *
     * @return \Ruga\Money\Amount
     */
    public function getAmount(): \Ruga\Money\Amount;
    
    
    
    /**
     * Returns calculated end amount after applying all the price parts
     * that are relevant for this component (price, position, basket).
     *
     * @return \Ruga\Money\Amount
     */
    public function getEndAmount(): \Ruga\Money\Amount;
    
    
    
    public function incl(): \Ruga\Money\Amount;
    
    
    
    public function excl(): \Ruga\Money\Amount;
    
    
    
    public function dump_includes(): array;
    
    
    
    public function dump_excludes(): array;
    
    
    
    /**
     * Return all the price parts included in the price.
     *
     * @return PricePartInterface[]
     */
    public function getIncludes(): array;
    
    
    
    /**
     * Return all the price parts NOT included in the price.
     *
     * @return PricePartInterface[]
     */
    public function getExcludes(): array;

}
