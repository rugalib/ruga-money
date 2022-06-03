<?php

declare(strict_types=1);

namespace Ruga\Money\PricePart;


/**
 * Interface to a price part.
 *
 * @see      Price
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
abstract class PricePartOperation
{
    const ADD = 'add';
    const SUB = 'sub';
}
