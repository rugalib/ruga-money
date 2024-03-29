<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Money\Test;

use Ruga\Money\Basket;
use Ruga\Money\Basket\BasketableInterface;
use Ruga\Money\PricePart\PricePartInterface;

/**
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class BasketTest extends \Ruga\Money\Test\PHPUnit\AbstractTestSetUp
{
    public function testCanCreateBill(): void
    {
        // $ba is the main document (example: bill)
        $ba = new \Ruga\Money\Basket();
        // create positions that are later filled with values from other positions price parts
        $vat = [
            '7.7' => new \Ruga\Money\Position(new \Ruga\Money\Price("0", 'CHF'), "0"),
            '5' => new \Ruga\Money\Position(new \Ruga\Money\Price("0", 'CHF'), "0"),
            '2.2' => new \Ruga\Money\Position(new \Ruga\Money\Price("0", 'CHF'), "0"),
        ];
        
        // $ba1 is a sub basket (sub document/position of multiple position)
        $ba1 = new \Ruga\Money\Basket();
        
        $p1_1 = new \Ruga\Money\Price("800", "CHF");
        $p1_1->first(new \Ruga\Money\PricePart\Percent('LSVA', 2)); // 2% LSVA included in price
        $pos1_1 = new \Ruga\Money\Position($p1_1, 1); // one piece of CHF 800
        // subtract 5% discount
        $pos1_1->then(new \Ruga\Money\PricePart\Percent('RABATT', 5, \Ruga\Money\PricePart\PricePartOperation::SUB));
        // add 7.7% VAT to position
        $pos1_1->then((new \Ruga\Money\PricePart\Percent('VAT', '7.7'))->output($vat['7.7'])); // CHF 58.52
        // add position to sub basket
        $ba1[] = $pos1_1; // CHF 760.00
        
        
        $p1_2 = new \Ruga\Money\Price("27.8", "CHF");
        $pos1_2 = (new \Ruga\Money\Position($p1_2, "10")) // 10 pieces of CHF 27.80
        // add 5% VAT to position
        ->then((new \Ruga\Money\PricePart\Percent('VAT', '5'))->output($vat['5'])); // CHF 13.90
        // add position to sub basket
        $ba1[] = $pos1_2; // CHF 278.00
        
        
        $p1 = new \Ruga\Money\Price("2345.50", "CHF");
        $p1->first(new \Ruga\Money\PricePart\Percent('LSVA', 2)); // 2% LSVA included in price
        $p1->first(
            new \Ruga\Money\PricePart\Absolute('ASSEMBLY', new \Ruga\Money\Amount("80", 'CHF'))
        ); // CHF 80 assembly cost included in price
        $pos1 = new \Ruga\Money\Position($p1, 2.5); // 2.5 pieces of CHF 2345.50
        // subtract discount of 5%
        $pos1->then(new \Ruga\Money\PricePart\Percent('RABATT', 5, \Ruga\Money\PricePart\PricePartOperation::SUB));
        // add 7.7% VAT to position
        $pos1->then((new \Ruga\Money\PricePart\Percent('VAT', '7.7'))->output($vat['7.7'])); // CHF 428.9333125
        $ba[] = $pos1; // CHF 5570.5625
        
        
        $p2 = new \Ruga\Money\Price("1.2345", "CHF");
        $pos2 = new \Ruga\Money\Position($p2, "150"); // 150 pieces of CHF 1.2345
        // subtract discount of 5% from position
        $pos2->then(new \Ruga\Money\PricePart\Percent('RABATT', 5, \Ruga\Money\PricePart\PricePartOperation::SUB));
        // add 2.2% VAT to position
        $pos2->then((new \Ruga\Money\PricePart\Percent('VAT', '2.2'))->output($vat['2.2'])); // CHF 3.8701575
        $ba[] = $pos2; // CHF 175.91625
        
        // Add the sub basket to the main basket
        $ba[] = $ba1; // CHF 1038.00
        
        // Add the "output" positions to the main document
        $ba[] = $vat['7.7']; // CHF 487.4533125
        $ba[] = $vat['5']; // CHF 13.90
        $ba[] = $vat['2.2']; // CHF 3.8701575
        
        // 5570.5625+175.91625+1038.00+487.4533125+13.90+3.8701575
        // = 7289.70222
        
        // Add another absolute pricepart
        $ba->then(new \Ruga\Money\PricePart\Absolute('SHIPPING', new \Ruga\Money\Amount("8", 'CHF')));
        // = 7297.70222
        
        echo '$ba=' . $ba;
        echo PHP_EOL;
        echo '$ba->excl()=' . $ba->excl();
        echo PHP_EOL;
        echo '$ba->incl()=' . $ba->incl();
        $this->assertSame('CHF 7’297.70', "{$ba->incl()}");
        echo PHP_EOL;
        echo '$ba->explain():' . PHP_EOL . $ba->explain();
        echo PHP_EOL;
        echo '$ba->incl()->roundingDiff()->formatAmount()=' . $ba->incl()->roundingDiff()->formatAmount();
        //echo '$ba->dump():' . PHP_EOL . print_r($ba->dump(), true);
        //echo 'var_dump($ba):' . PHP_EOL . var_dump($ba, true);
        echo PHP_EOL . PHP_EOL;
    }
    
    
    
    public function createBasket(): Basket
    {
        // $ba is the main document (example: bill)
        $ba = new \Ruga\Money\Basket();
        // create positions that are later filled with values from other positions price parts
        $vat = [
            '7.7' => new \Ruga\Money\Position(new \Ruga\Money\Price("0", 'CHF'), "0"),
            '5' => new \Ruga\Money\Position(new \Ruga\Money\Price("0", 'CHF'), "0"),
            '2.2' => new \Ruga\Money\Position(new \Ruga\Money\Price("0", 'CHF'), "0"),
        ];
        
        // $ba1 is a sub basket (sub document/position of multiple position)
        $ba1 = new \Ruga\Money\Basket();
        
        $p1_1 = new \Ruga\Money\Price("800", "CHF");
        $p1_1->first(new \Ruga\Money\PricePart\Percent('LSVA', 2)); // 2% LSVA included in price
        $pos1_1 = new \Ruga\Money\Position($p1_1, 1); // one piece of CHF 800
        // subtract 5% discount
        $pos1_1->then(new \Ruga\Money\PricePart\Percent('RABATT', 5, \Ruga\Money\PricePart\PricePartOperation::SUB));
        // add 7.7% VAT to position
        $pos1_1->then((new \Ruga\Money\PricePart\Percent('VAT', '7.7'))->output($vat['7.7'])); // CHF 58.52
        // add position to sub basket
        $ba1[] = $pos1_1; // CHF 760.00
        
        
        $p1_2 = new \Ruga\Money\Price("27.8", "CHF");
        $pos1_2 = (new \Ruga\Money\Position($p1_2, "10")) // 10 pieces of CHF 27.80
        // add 5% VAT to position
        ->then((new \Ruga\Money\PricePart\Percent('VAT', '5'))->output($vat['5'])); // CHF 13.90
        // add position to sub basket
        $ba1[] = $pos1_2; // CHF 278.00
        
        
        $p1 = new \Ruga\Money\Price("2345.50", "CHF");
        $p1->first(new \Ruga\Money\PricePart\Percent('LSVA', 2)); // 2% LSVA included in price
        $p1->first(
            new \Ruga\Money\PricePart\Absolute('ASSEMBLY', new \Ruga\Money\Amount("80", 'CHF'))
        ); // CHF 80 assembly cost included in price
        $pos1 = new \Ruga\Money\Position($p1, 2.5); // 2.5 pieces of CHF 2345.50
        // subtract discount of 5%
        $pos1->then(new \Ruga\Money\PricePart\Percent('RABATT', 5, \Ruga\Money\PricePart\PricePartOperation::SUB));
        // add 7.7% VAT to position
        $pos1->then((new \Ruga\Money\PricePart\Percent('VAT', '7.7'))->output($vat['7.7'])); // CHF 428.9333125
        $ba[] = $pos1; // CHF 5570.5625
        
        
        $p2 = new \Ruga\Money\Price("1.2345", "CHF");
        $pos2 = new \Ruga\Money\Position($p2, "150"); // 150 pieces of CHF 1.2345
        // subtract discount of 5% from position
        $pos2->then(new \Ruga\Money\PricePart\Percent('RABATT', 5, \Ruga\Money\PricePart\PricePartOperation::SUB));
        // add 2.2% VAT to position
        $pos2->then((new \Ruga\Money\PricePart\Percent('VAT', '2.2'))->output($vat['2.2'])); // CHF 3.8701575
        $ba[] = $pos2; // CHF 175.91625
        
        // Add the sub basket to the main basket
        $ba[] = $ba1; // CHF 1038.00
        
        // Add the "output" positions to the main document
        $ba[] = $vat['7.7']; // CHF 487.4533125
        $ba[] = $vat['5']; // CHF 13.90
        $ba[] = $vat['2.2']; // CHF 3.8701575
        
        // 5570.5625+175.91625+1038.00+487.4533125+13.90+3.8701575
        // = 7289.70222
        
        // Add another absolute pricepart
        $ba->then(new \Ruga\Money\PricePart\Absolute('SHIPPING', new \Ruga\Money\Amount("8", 'CHF')));
        // = 7297.70222
        return $ba;
    }
    
    
    
    public function writePosition(\Ruga\Money\Position $position)
    {
        $str = '';
        
        $str .= sprintf(
                FMT,
                $position->getPosId(),
                'Bezeichnung',
                $position->getPrice()->getEndAmount()->formatAmount(),
                $position->getQuantity(),
                '',
                $position->getEndAmount()->formatAmount()
            ) . PHP_EOL;
        
        $str_inkl = '';

// 	foreach( $position->getPrice()->dump_includes() as $a )
// 	{
// 		$str_inkl="      |  Preis inkl. {$a['value']} {$a['name']} ({$a['calculation']})" . PHP_EOL . $str_inkl;
// 	}
        /** @var PricePartInterface $pricepart */
        foreach ($position->getPrice()->getIncludes() as $pricepart) {
            $str_inkl = "      |  Preis inkl. {$pricepart->getValue()} {$pricepart->getName()} ({$pricepart->getCalculation()})" . PHP_EOL . $str_inkl;
        }
        
        
        $str .= $str_inkl;
        $str_inkl = '';
        foreach ($position->dump_includes() as $a) {
            $str_inkl = "      |  Position inkl. {$a['value']} {$a['name']} ({$a['calculation']})" . PHP_EOL . $str_inkl;
        }
        $str .= $str_inkl;
        
        foreach ($position->getPrice()->dump_excludes() as $a) {
            $str .= "      |  Preis exkl. {$a['value']} {$a['name']} ({$a['calculation']})" . PHP_EOL;
        }
        foreach ($position->dump_excludes() as $a) {
            $str .= "      |  Position exkl. {$a['value']} {$a['name']} ({$a['calculation']})" . PHP_EOL;
        }
        
        $str .= sprintf(
                FMT,
                '',
                '',
                '',
                '',
                '',
                ''
            ) . PHP_EOL;
        
        
        return $str;
    }
    
    
    
    public function writeBasket(\Ruga\Money\Basket $basket)
    {
        $str = '';
        
        if ($basket->getLevel() > 0) {
            $str .= sprintf(
                    FMT,
                    $basket->getPosId(),
                    'Gliederungssumme',
                    '',
                    '',
                    '',
                    ''
                ) . PHP_EOL;
        }
        
        /** @var BasketableInterface $item */
        foreach ($basket as $item) {
            if (is_a($item, \Ruga\Money\Basket::class)) {
                $str .= $this->writeBasket($item);
            } else {
                $str .= $this->writePosition($item);
            }
        }
        
        $str .= sprintf(
                FMT,
                '',
                '',
                '',
                '',
                '',
                $basket->getAmount()->rounded()->formatAmount()
            ) . PHP_EOL;
        
        $str_inkl = '';
        foreach ($basket->dump_includes() as $a) {
            $str_inkl = "      |  Block inkl. {$a['value']} {$a['name']} ({$a['calculation']})" . PHP_EOL . $str_inkl;
        }
        $str .= $str_inkl;
        foreach ($basket->dump_excludes() as $a) {
            $str .= "      |  Block exkl. {$a['value']} {$a['name']} ({$a['calculation']})" . PHP_EOL;
        }
        
        
        $str .= sprintf(
                FMT,
                '',
                '',
                '',
                '',
                '',
                '----------'
            ) . PHP_EOL;
        $str .= sprintf(
                FMT,
                '',
                '',
                '',
                '',
                '',
                ''
            ) . PHP_EOL;
        
        
        return $str;
    }
    
    
    
    public function testCanPrintBill(): void
    {
        define("FMT", "%5s | %-40.20s | %10s | %5s | %5s | %10s");
        
        $ba = $this->createBasket();
        echo $this->writeBasket($ba);
    
        echo sprintf(
                FMT,
                '',
                'exkl.',
                '',
                '',
                '',
                $ba->excl()->rounded()->formatAmount()
            ) . PHP_EOL;
        
        echo sprintf(
                FMT,
                '',
                'inkl.',
                '',
                '',
                '',
                $ba->incl()->rounded()->formatAmount()
            ) . PHP_EOL;
        
        echo PHP_EOL;
        $this->assertSame('CHF 7’297.70', "{$ba->incl()}");
    }
    
    
}
