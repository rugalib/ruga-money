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
    
    
    
    public function testCanSubAmount(): void
    {
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $a = $a->sub(5);
        $this->assertSame('12.50000000', $a->getAmountRaw());
        
        $a = new \Ruga\Money\Amount(17.5, 'CHF', ['scale' => 8]);
        $a = $a->sub(new \Ruga\Money\Amount(5, 'CHF'));
        $this->assertSame('12.50000000', $a->getAmountRaw());
        
        $this->expectException(CurrencyMismatchException::class);
        $a = new \Ruga\Money\Amount(17.5, 'CHF', ['scale' => 8]);
        $a = $a->sub(5);
        $this->assertSame('12.50000000', $a->getAmountRaw());
    }
    
    
    
    public function testCanMulAmount(): void
    {
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $a = $a->mul("5");
        $this->assertSame('87.50000000', $a->getAmountRaw());
        
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $a = $a->mul((int)6);
        $this->assertSame('105.00000000', $a->getAmountRaw());
        
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $a = $a->mul(1.077);
        $this->assertSame('18.84750000', $a->getAmountRaw());
    }
    
    
    
    public function testCanDivAmount(): void
    {
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $a = $a->div("5");
        $this->assertSame('3.50000000', $a->getAmountRaw());
        
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $a = $a->div((int)6);
        $this->assertSame('2.91666666', $a->getAmountRaw());
        
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $a = $a->div(1.077);
        $this->assertSame('16.24883936', $a->getAmountRaw());
    }
    
    
    
    public function testCanCompareAmount(): void
    {
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $c = $a->comp(5);
        $this->assertSame(1, $c);
        
        $a = new \Ruga\Money\Amount(17.5, 'CHF', ['scale' => 8]);
        $c = $a->comp(new \Ruga\Money\Amount(17.5, 'CHF'));
        $this->assertSame(0, $c);
        
        $a = new \Ruga\Money\Amount(17.5, null, ['scale' => 8]);
        $c = $a->comp("50");
        $this->assertSame(-1, $c);
        
        $this->expectException(CurrencyMismatchException::class);
        $a = new \Ruga\Money\Amount(17.5, 'CHF', ['scale' => 8]);
        $c = $a->comp(5);
        $this->assertSame(0, $c);
    }
    
    
    
    public function testCanConvertCurrency(): void
    {
        $a = new \Ruga\Money\Amount(17.5, 'CHF', ['scale' => 8]);
        $b = $a->convertTo('EUR', 1/1.06);
        $this->assertSame('16.50943396', $b->getAmountRaw());
    }
    
    
    
    public function testCanToggleSymbol(): void
    {
        $a = new \Ruga\Money\Amount(17.5, 'CHF');
        $this->assertSame('CHF 17.50', "{$a}");
        
        $a = new \Ruga\Money\Amount(17.5, 'CHF', ['showSymbol' => false]);
        $this->assertSame('17.50', "{$a}");
        
        $a = new \Ruga\Money\Amount(17.5, 'CHF', ['showSymbol' => true]);
        $this->assertSame('CHF 17.50', "{$a}");
        
        $a = new \Ruga\Money\Amount(17.5, 'CHF');
        $a->showSymbol(false);
        $this->assertSame('17.50', "{$a}");
        
        $a->showSymbol(true);
        $this->assertSame('CHF 17.50', "{$a}");
    }
    
    
    
    public function testCanGetRoundingDiff(): void
    {
        $a = new \Ruga\Money\Amount(17.5, 'CHF');
        $b = $a->mul(1.077);
        echo "Value: {$b->getAmountRaw()}" . PHP_EOL;
        $this->assertSame('18.8475', "{$b->getAmountRaw()}");
        
        echo "Rounded value: {$b->rounded()->getAmountRaw()}" . PHP_EOL;
        $this->assertSame('18.85', "{$b->rounded()->getAmountRaw()}");
        
        $d = $b->roundingDiff();
        echo "Difference: {$d->getAmountRaw()}" . PHP_EOL;
        $this->assertSame('0.0025', "{$d->getAmountRaw()}");
    }
    
    
    
    public function testCanPrintAmount(): void
    {
        $a = new \Ruga\Money\Amount(17.5, 'CHF');
        $b = $a->mul(1.077);
        $this->assertSame('CHF 18.85', "{$b}");
        
        $a = new \Ruga\Money\Amount(17.5, 'CHF');
        $a->showSymbol(false);
        $b = $a->mul(1.077);
        $this->assertSame('18.85', "{$b}");
        
        $a = new \Ruga\Money\Amount(17.5);
        $b = $a->mul(1.077);
        $this->assertSame('18.8475', "{$b}");
    }
    
    
}
