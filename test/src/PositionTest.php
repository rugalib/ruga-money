<?php

declare(strict_types=1);

namespace Ruga\Money\Test;

use Ruga\Money\Position;
use Ruga\Money\Price;

/**
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class PositionTest extends \Ruga\Money\Test\PHPUnit\AbstractTestSetUp
{
    public function testCanCreatePosition(): void
    {
        $p1 = (new \Ruga\Money\Price("2345.50", "CHF"))
            ->first(new \Ruga\Money\PricePart\Percent('LSVA', 2))
            ->first(new \Ruga\Money\PricePart\Absolute('ASSEMBLY', new \Ruga\Money\Amount("80", 'CHF')))
            ->then(new \Ruga\Money\PricePart\Absolute('SHIPPING', new \Ruga\Money\Amount("100", 'CHF')))
            ->then(
                new \Ruga\Money\PricePart\Absolute(
                    'RABATT',
                    new \Ruga\Money\Amount("50", 'CHF'),
                    \Ruga\Money\PricePart\PricePartOperation::SUB
                )
            )
            ->then(new \Ruga\Money\PricePart\Percent('VAT', '7.7'));
        $this->assertInstanceOf(Price::class, $p1);
        
        $pos = (new \Ruga\Money\Position($p1, 2.5))
            ->then(new \Ruga\Money\PricePart\Percent('RABATT', 5, \Ruga\Money\PricePart\PricePartOperation::SUB));
        $this->assertInstanceOf(Position::class, $pos);
        
        echo '$pos=' . $pos;
        echo PHP_EOL;
        echo '$pos->excl()=' . $pos->excl();
        echo PHP_EOL;
        echo '$pos->incl()=' . $pos->incl();
        $this->assertSame('CHF 5’570.56', "{$pos->incl()}");
        echo PHP_EOL;
        echo '$pos->explain():' . PHP_EOL . $pos->explain();
        echo PHP_EOL . PHP_EOL;
    }
}
