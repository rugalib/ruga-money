<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Money\PricePart;

/**
 * Price part that gets his amount from another price part.
 * This is used as a placeholder for price parts that are
 * defined elsewhere and place their output here.
 *
 * @see      AbstractPricePart
 * @see      PricePartInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class Link extends AbstractPricePart implements PricePartInterface
{
    /**
     * Link to the real price part.
     *
     * @var PricePartInterface
     */
    private $linkTo = null;
    
    
    
    /**
     * Initialize percent price part.
     *
     * @param PricePartInterface $linkTo
     */
    public function __construct(PricePartInterface $linkTo)
    {
        parent::__construct($linkTo->getName(), "LINK", $linkTo->getOperation());
        $this->linkTo = $linkTo;
    }
    
    
    
    /**
     *
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\AbstractPricePart::getValue()
     */
    public function getValue(): string
    {
        return $this->getAbsoluteAmount(new \Ruga\Money\Amount("0"))->__toString();
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::getAbsoluteAmount()
     */
    public function getAbsoluteAmount(): \Ruga\Money\Amount
    {
        return $this->linkTo->getAbsoluteAmount();
    }
    
    
}

