<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Money\PricePart;

/**
 * Price part represented as a absolute value.
 *
 * @see      AbstractPricePart
 * @see      PricePartInterface
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class Absolute extends AbstractPricePart implements PricePartInterface
{
    /**
     * Stores absolute Amount.
     *
     * @var \Ruga\Money\Amount
     */
    private $amount = null;
    
    
    
    /**
     * Initialize absolute price part.
     *
     * @param string             $name
     * @param \Ruga\Money\Amount $amount
     * @param string             $operation
     */
    public function __construct(string $name, \Ruga\Money\Amount $amount, string $operation = PricePartOperation::ADD)
    {
        parent::__construct($name, "{$amount}", $operation);
        $this->amount = $amount;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Ruga\Money\PricePart\PricePartInterface::getAbsoluteAmount()
     */
    public function getAbsoluteAmount(): \Ruga\Money\Amount
    {
        return $this->amount;
    }
}
