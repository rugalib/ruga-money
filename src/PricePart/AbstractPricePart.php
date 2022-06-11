<?php

declare(strict_types=1);

namespace Ruga\Money\PricePart;

use Ruga\Money\Basket\DumpableInterface;
use Ruga\Money\Amount;

/**
 * An abstract price part.
 *
 * @see      Price
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
abstract class AbstractPricePart implements PricePartInterface, DumpableInterface
{
    use \Ruga\Std\Chain\LinkTrait;
    
    private ?string $name = null;
    private ?string $value = null;
    private ?string $operation = null;
    /**
     * @deprecated
     * @var bool
     */
    private bool $pricedIn = false;
    
    private $outputTo = null;
    
    
    
    /**
     * Initialize price part.
     *
     * @param string $name
     * @param string $operation
     */
    public function __construct(string $name, string $value, string $operation)
    {
        $this->name = $name;
        $this->value = $value;
        $this->operation = $operation;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::getName()
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::getValue()
     */
    public function getValue(): string
    {
        return $this->value;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::getCalculation()
     */
    public function getCalculation(): string
    {
        if($this->isIncl()) {
            if (empty($this->outputTo)) {
                return "{$this->getAmount()} " .
                    ($this->getOperation() == PricePartOperation::ADD ? '+' : '-') .
                    " {$this->getValue()} = {$this->getBaseAmount()}";
        
            } else {
                return "{$this->getValue()} of {$this->getAmount()} = {$this->getAbsoluteAmount()}";
            }
        }
        
        if($this->isExcl()) {
            if (empty($this->outputTo)) {
                return "{$this->getBaseAmount()} " .
                    ($this->getOperation() == PricePartOperation::ADD ? '+' : '-') .
                    " {$this->getValue()} = {$this->getAmount()}";
        
            } else {
                return "{$this->getValue()} of {$this->getBaseAmount()} = {$this->getAbsoluteAmount()}";
            }
        }
        
        return "==[ Price ]==> {$this->getAmount()}";
    }
    
    
    
    public function dump(): array
    {
        return [
            'type' => static::class,
            'name' => $this->getName(),
            'operation' => $this->getOperation(),
            'link_pos' => $this->linkPos(),
        ];
    }
    
    
    
    /**
     * Returns the new amount after this price part is applied.
     * Calculate the other way (up > down) if $excl is true.
     *
     * @param \Ruga\Money\Amount $amount
     * @param bool               $excl
     *
     * @return \Ruga\Money\Amount
     */
    public function getNewAmount(\Ruga\Money\Amount $amount, bool $excl = false): \Ruga\Money\Amount
    {
        if (!empty($this->outputTo)) {
            return $amount;
        }
        
        if ($this->getOperation($excl) == PricePartOperation::ADD) {
            return $amount->add($this->getAbsoluteAmount($amount, $excl));
        } else {
            return $amount->sub($this->getAbsoluteAmount($amount, $excl));
        }
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::getBaseAmount()
     */
    public function getBaseAmount(): \Ruga\Money\Amount
    {
        $excl = $this->linkPos() < 0;
        if ($excl) {
            $amount = $this->nextLink()->getAmount();
        } else {
            $amount = $this->prevLink()->getAmount();
        }
        return $amount;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Std\Chain\LinkInterface::getAmount()
     */
    public function getAmount(): \Ruga\Money\Amount
    {
// 		echo PHP_EOL;
// 		echo get_called_class() . "::getAmount()" . PHP_EOL;
// 		echo print_r($this->dump(), true) . PHP_EOL;
// 		echo "pos: {$this->linkPos()} | prev: " . ($this->linkTrait_prevLink ? get_class($this->linkTrait_prevLink) : 'null') . " | next: " . ($this->linkTrait_nextLink ? get_class($this->linkTrait_nextLink) : 'null') . PHP_EOL;
        $excl = $this->linkPos() < 0;
        return $this->getNewAmount($this->getBaseAmount(), $excl);
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::getOperation()
     */
    public function getOperation(bool $excl = false): string
    {
        if (!$excl) {
            return $this->operation;
        }
        if ($this->operation == PricePartOperation::ADD) {
            return PricePartOperation::SUB;
        } else {
            return PricePartOperation::ADD;
        }
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::isPricedIn()
     * @deprecated
     */
    public function isPricedIn(): bool
    {
        return $this->pricedIn;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @return bool
     */
    public function isIncl(): bool
    {
        return $this->linkPos() < 0;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @return bool
     */
    public function isExcl(): bool
    {
        return $this->linkPos() > 0;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::output()
     */
    public function output(PricePartableInterface $output): PricePartInterface
    {
        $this->outputTo = $output;
        $this->outputTo->then(new \Ruga\Money\PricePart\Link($this));
        return $this;
    }
    
    
}
