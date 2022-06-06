<?php

declare(strict_types=1);

namespace Ruga\Money\Basket;

/**
 * Provides functions for object that can be put in a basket.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * @see BasketableInterface
 */
trait BasketableTrait
{
    /**
     * Stores posId if element is in a basket.
     *
     * @var string
     */
    private $basketable_posId = '';
    
    private $basketable_parentPosId = '';
    
    private $basketable_level = 0;
    
    
    
    /**
     * Returns a identification string representing the tree-position of the element.
     *
     * @return string
     */
    public function getPosId(): string
    {
        return implode(
            '.',
            array_filter([$this->basketable_parentPosId, $this->basketable_posId], function ($val) {
                return ($val !== '') && ($val !== null);
            })
        );
    }
    
    
    
    /**
     * Set id.
     *
     * @param string $posId
     * @param string $parentPosId
     */
    public function setPosId(string $posId, string $parentPosId = '')
    {
        $this->basketable_posId = $posId;
        $this->basketable_parentPosId = $parentPosId;
        if (is_a($this, \Ruga\Money\Basket::class)) {
            foreach ($this as $item) {
                $item->setPosId($item->getPosId(), $this->getPosId());
            }
        }
    }
    
    
    
    /**
     * Returns the level of the object in the tree.
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->basketable_level;
    }
    
    
    
    /**
     * Set level.
     *
     * @param int $level
     */
    public function setLevel(int $level)
    {
        $this->basketable_level = $level;
        if (is_a($this, \Ruga\Money\Basket::class)) {
            foreach ($this as $item) {
                $item->setLevel($this->getLevel() + 1);
            }
        }
    }
    
}
