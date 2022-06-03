<?php

declare(strict_types=1);

namespace Ruga\Money\Basket;

/**
 * Interface to an object that can be put in a basket.
 *
 * @see      Basket
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface DumpableInterface
{
    public function dump(): array;
}
