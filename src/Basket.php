<?php

declare(strict_types=1);

namespace Ruga\Money;

/**
 * Stores a position (price and quantity) of a certain good.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class Basket implements Basket\DumpableInterface, Basket\BasketableInterface, PricePart\PricePartableInterface,
                        \Ruga\Std\Chain\AnchorInterface, \Countable, \Iterator, \ArrayAccess
{
    use PricePart\PricePartableTrait;
    use Basket\CountableTrait;
    use Basket\IteratorTrait;
    use Basket\ArrayAccessTrait;
    use Basket\BasketableTrait;
    use \Ruga\Std\Chain\LinkTrait;
    
    
    /**
     * Holds filtered data.
     *
     * @var mixed
     */
    private $DataProvider_DATA = [];
    
    
    
    public function dump(): array
    {
        $a = [
            'type' => self::class,
            'pos_id' => $this->getPosId(),
            'level' => $this->getLevel(),
            'total_excl' => "{$this->excl()}",
            'total' => "{$this->getAmount()}",
            'total_incl' => "{$this->incl()}",
            'includes' => $this->dump_includes(),
            'excludes' => $this->dump_excludes(),
            'items' => [],
        ];
        
        /** @var Basket\BasketableInterface $item */
        foreach ($this as $item) {
            $a['items'][] = $item->dump();
        }
        return $a;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartableInterface::getAmount()
     */
    public function getAmount(): \Ruga\Money\Amount
    {
        $amount = null;
        /** @var Basket\BasketableInterface $item */
        /** @var Basket $item */
        /** @var Position $item */
        foreach ($this as $item) {
            $amount = ($amount === null) ? $item->getEndAmount() : $item->getEndAmount()->add($amount);
        }
        
        // If basket contains no positions, set amount to 0
        if (!$amount) {
            $amount = new Amount(0);
        }
        
        return $amount;
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
