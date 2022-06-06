<?php

declare(strict_types=1);

namespace Ruga\Money\Basket;

/**
 * Interface to a object that can be put in a basket.
 *
 * @see      Basket, BasketableInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface BasketableInterface
{
    /**
     * Returns base amount of the item in the basket.
     *
     * @return \Ruga\Money\Amount
     */
    public function getAmount(): \Ruga\Money\Amount;
    
    
    
    /**
     * Returns a identification string representing the tree-position of the element.
     *
     * @return string
     */
    public function getPosId(): string;
    
    
    
    /**
     * Set id.
     *
     * @param string $posId
     * @param string $parentPosId
     */
    public function setPosId(string $posId, string $parentPosId = '');
    
    
    
    /**
     * Returns the level of the object in the tree.
     *
     * @return int
     */
    public function getLevel(): int;
    
    
    
    /**
     * Set level.
     *
     * @param int $level
     */
    public function setLevel(int $level);
}
