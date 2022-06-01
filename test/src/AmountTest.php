<?php

declare(strict_types=1);

namespace Ruga\Money\Test;

use Laminas\ServiceManager\ServiceManager;
use Ruga\Money\Amount;
use Ruga\Money\Exception\CurrencyMismatchException;
use Ruga\Money\Exception\CurrencyNotSetException;

/**
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class AmountTest extends \Ruga\Money\Test\PHPUnit\AbstractTestSetUp
{
    public function testCanSetAmountFromFloat(): void
    {
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $this->assertSame('17.50000000', $a->getAmountRaw());
        
        $a = new \Ruga\Money\Amount(28.99, 'EUR');
        $this->assertSame('EUR 28.9900', $a->formatCurrency());
        
        $this->expectException(CurrencyNotSetException::class);
        $a = new \Ruga\Money\Amount(28.95);
        $this->assertSame('17.50000000', $a->formatCurrency());
    }
    
    
    
    public function testCanSetAmountFromString(): void
    {
        $a = new \Ruga\Money\Amount("8.95", null, ['scale' => 8]);
        $this->assertSame('8.95000000', $a->getAmountRaw());
        
        $a = new \Ruga\Money\Amount("28.99", 'EUR');
        $this->assertSame('EUR 28.9900', $a->formatCurrency());
        
        $this->expectException(CurrencyNotSetException::class);
        $a = new \Ruga\Money\Amount("28.95");
        $this->assertSame('17.50000000', $a->formatCurrency());
    }
    
    
    
    public function testCanSetAmountFromInt(): void
    {
        $a = new \Ruga\Money\Amount(1234, null, ['scale' => 8]);
        $this->assertSame('1234.00000000', $a->getAmountRaw());
        
        $a = new \Ruga\Money\Amount(234, 'EUR');
        $this->assertSame('EUR 234.0000', $a->formatCurrency());
        
        $this->expectException(CurrencyNotSetException::class);
        $a = new \Ruga\Money\Amount(567);
        $this->assertSame('567.0000', $a->formatCurrency());
    }
    
    
    
    public function testCanAddAmount(): void
    {
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $a = $a->add(5);
        $this->assertSame('22.50000000', $a->getAmountRaw());
    
        $a = new \Ruga\Money\Amount(17.5, 'CHF', ['scale' => 8]);
        $a = $a->add(new \Ruga\Money\Amount(5, 'CHF'));
        $this->assertSame('22.50000000', $a->getAmountRaw());
    
        $this->expectException(CurrencyMismatchException::class);
        $a = new \Ruga\Money\Amount(17.5, 'CHF', ['scale' => 8]);
        $a = $a->add(5);
        $this->assertSame('22.50000000', $a->getAmountRaw());
    }
}
