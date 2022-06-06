<?php

namespace Ruga\Money\Basket;

/**
 * Provides countable functions to the data provider.
 *
 * @see      \Countable
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
trait CountableTrait
{
    /**
     * Counts the number of records in the private $data array.
     *
     * @return int Number of records
     */
    public function count(): int
    {
        return count($this->DataProvider_DATA);
    }
}
