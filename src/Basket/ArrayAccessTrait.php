<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Money\Basket;

/**
 * Provides array access functions to the data provider.
 *
 * @see      \ArrayAccess
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
trait ArrayAccessTrait
{
    /**
     * Whether an offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->DataProvider_DATA);
    }
    
    
    
    /**
     * Offset to retrieve.
     *
     * @param string $offset
     *
     * @return BasketableInterface
     */
    public function offsetGet($offset): ?BasketableInterface
    {
        return $this->offsetExists($offset) ? $this->DataProvider_DATA[$offset] : null;
    }
    
    
    
    /**
     * Assign a value to the specified offset.
     * No function since data set is read only.
     *
     * @param string              $offset
     * @param BasketableInterface $value
     *
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        if (!is_a($value, BasketableInterface::class)) {
            throw new \Exception("Element must be of type BasketableInterface.");
        }
        if ($offset === null) {
            $this->DataProvider_DATA[] = $value;
            end($this->DataProvider_DATA);
            $offset = key($this->DataProvider_DATA);
        } else {
            $this->DataProvider_DATA[$offset] = $value;
        }
        
        $value->setPosId((string)$offset, $this->getPosId());
        $value->setLevel($this->getLevel() + 1);
    }
    
    
    
    /**
     * Unset an offset.
     * No function since data set is read only.
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->DataProvider_DATA[$offset]);
    }
}
