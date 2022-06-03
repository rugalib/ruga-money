<?php

declare(strict_types=1);

namespace Ruga\Money\PricePart;


/**
 * Price part represented as a percentage.
 *
 * @see      AbstractPricePart
 * @see      PricePartInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class Percent extends AbstractPricePart implements PricePartInterface
{
    private ?string $percentValue = null;
    private ?string $percentFactor = null;
    private ?string $percentFactorExcl = null;
    
    
    
    /**
     * Initialize percent price part.
     *
     * @param string           $name
     * @param string|int|float $value
     * @param string           $operation
     */
    public function __construct(string $name, $value, string $operation = PricePartOperation::ADD)
    {
        parent::__construct($name, "{$value}%", $operation);
        $value = (string)$value;
        $this->percentValue = $value;
        $this->percentFactor = bcdiv($value, "100", 20);
        $this->percentFactorExcl = bcadd("1", $this->percentFactor, 20);
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::getAbsoluteAmount()
     */
    public function getAbsoluteAmount(): \Ruga\Money\Amount
    {
        $excl = $this->linkPos() < 0;
        if ($excl) {
            $amount = $this->nextLink()->getAmount();
        } else {
            $amount = $this->prevLink()->getAmount();
        }
        
        if ($excl) {
            return $amount->sub($amount->div($this->percentFactorExcl));
        } else {
            return $amount->mul($this->percentFactor);
        }
    }
}

