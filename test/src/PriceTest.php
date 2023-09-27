<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Money\Test;

use Ruga\Money\Amount;
use Ruga\Money\Price;

/**
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class PriceTest extends \Ruga\Money\Test\PHPUnit\AbstractTestSetUp
{
    public function testCanCreatePrice(): void
    {
        $p = new Price(1727.55, 'CHF');
        $this->assertInstanceOf(Amount::class, $p->getAmount());
    }
    
    
    
    public function testCanDumpPrice(): void
    {
        $p = new Price(1727.55, 'CHF');
        $this->assertInstanceOf(Amount::class, $p->getAmount());
        echo print_r($p->dump(), true);
    }
    
    
    
    public function testCanPrintPrice(): void
    {
        $p = new Price(1727.55, 'CHF');
        $this->assertInstanceOf(Amount::class, $p->getAmount());
        $this->assertSame('CHF 1’727.55', "{$p}");
    }
    
    
    
    public function testCanChainPrices(): void
    {
        $p1 = new Price("2345.60", "CHF");
        $p1->first(new \Ruga\Money\PricePart\Percent('LSVA', 2))
            ->first(new \Ruga\Money\PricePart\Absolute('ASSEMBLY', new Amount("80", "CHF")));
        
        $p1->then(new \Ruga\Money\PricePart\Absolute('SHIPPING', new \Ruga\Money\Amount("100", 'CHF')))
            ->then(
                new \Ruga\Money\PricePart\Absolute(
                    'RABATT',
                    new \Ruga\Money\Amount("50", 'CHF'),
                    \Ruga\Money\PricePart\PricePartOperation::SUB
                )
            )
            ->then(new \Ruga\Money\PricePart\Percent('VAT', '7.7'));

//        print_r($p1->dump());
//        echo $p1->explain();
        
        echo '$p1=' . $p1;
        echo PHP_EOL;
        echo '$p1->excl()=' . $p1->excl();
        $this->assertSame('CHF 2’219.61', "{$p1->excl()}");
        echo PHP_EOL;
        echo '$p1->incl()=' . $p1->incl();
        $this->assertSame('CHF 2’580.06', "{$p1->incl()}");
        echo PHP_EOL;
        echo '$p1->explain():' . PHP_EOL . $p1->explain();
        echo PHP_EOL . PHP_EOL;
        echo '$p1->incl()->roundingDiff()->formatAmount()=' . $p1->incl()->roundingDiff()->formatAmount();
        echo PHP_EOL . PHP_EOL;
    }
    
    
}
