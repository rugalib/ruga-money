<?php

declare(strict_types=1);

namespace Ruga\Money\PricePart;

use Ruga\Std\Chain\Direction;

/**
 * Provides functions for object that can have price parts.
 *
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
trait PricePartableTrait
{
    public function getIncludes(): array
    {
        return $this->getChainLinksAsArray(Direction::BACKWARD());
        
        $a = [];
        $amount = $this->getAmount();
        $incl = array_reverse($this->getChainLinksAsArray());
        /** @var \Ruga\Money\PricePart\PricePartInterface $item */
        foreach ($incl as $item) {
            $a[] = $item;
        }
        return $a;
    }
    
    
    
    public function getExcludes(): array
    {
        return $this->getChainLinksAsArray();
    }
    
    
    
    public function dump_includes(): array
    {
        $a = [];
        $amount = $this->getAmount();
        $incl = $this->getIncludes();
        /** @var \Ruga\Money\PricePart\PricePartInterface $item */
        foreach ($incl as $item) {
            $b = [
                'type' => get_class($item),
                'name' => $item->getName(),
                'value' => $item->getValue(),
                'operation' => $item->getOperation(),
                'new_amount' => $amount,
                'absolute_amount' => $item->getAbsoluteAmount($amount, true),
                'base_amount' => ($amount = $item->getAmount()),
            ];
// 			if(empty($this->outputTo))
// 				$b['calculation']="{$b['base_amount']} " . ($b['operation']==PricePartOperation::ADD ? '+' : '-') . " {$b['value']} = {$b['new_amount']}";
// 			else
// 				$b['calculation']="{$b['value']} of {$b['base_amount']} = {$b['absolute_amount']}";
            $b['calculation'] = $item->getCalculation();
            $a[] = $b;
        }
        return $a;
    }
    
    
    
    public function dump_excludes(): array
    {
        $a = [];
        $amount = $this->getAmount();
        /** @var \Ruga\Money\PricePart\PricePartInterface $item */
        $excl = $this->getExcludes();
        foreach ($excl as $item) {
            $b = [
                'type' => get_class($item),
                'name' => $item->getName(),
                'value' => $item->getValue(),
                'operation' => $item->getOperation(),
                'base_amount' => $amount,
                'absolute_amount' => $item->getAbsoluteAmount($amount),
                'new_amount' => ($amount = $item->getAmount()),
            ];
// 			$b['calculation']="{$b['base_amount']} " . ($b['operation']==PricePartOperation::ADD ? '+' : '-') . " {$b['value']} = {$b['new_amount']}";
// 			if(empty($this->outputTo))
// 				$b['calculation']="{$b['base_amount']} " . ($b['operation']==PricePartOperation::ADD ? '+' : '-') . " {$b['value']} = {$b['new_amount']}";
// 			else
// 				$b['calculation']="{$b['value']} of {$b['base_amount']} = {$b['absolute_amount']}";
            $b['calculation'] = $item->getCalculation();
            $a[] = $b;
        }
        return $a;
    }
    
    
    
    /**
     * Calculate the price excluding all price parts.
     *
     * @return \Ruga\Money\Amount
     */
    public function excl(): \Ruga\Money\Amount
    {
        return $this->leftEndLink()->getAmount();
    }
    
    
    
    /**
     * Calculate the price including all price parts.
     *
     * @return \Ruga\Money\Amount
     */
    public function incl(): \Ruga\Money\Amount
    {
        return $this->rightEndLink()->getAmount();
    }
    
    
    
    public function getEndAmount(): \Ruga\Money\Amount
    {
        return $this->incl();
    }
    
    
    
    /**
     * Explain the price with all included and excluded parts.
     *
     * @return string
     */
    public function explain()
    {
        $str = '';
        
        $amount = $this->excl();
        $incl = array_reverse($this->getIncludes());
        /** @var PricePartInterface $pricepart */
        foreach ($incl as $pricepart) {
            $str .= "name={$pricepart->getName()} | operation={$pricepart->getOperation()} | absoluteAmount: {$pricepart->getAbsoluteAmount($amount)} | ";
            $str .= "{$amount} " . ($pricepart->getOperation(
                ) == PricePartOperation::ADD ? '+' : '-') . " {$pricepart->getAbsoluteAmount($amount)} = ";
            $amount = $pricepart->getNewAmount($amount);
            $str .= "{$amount}";
            $str .= PHP_EOL;
        }
        
        $str .= "=> {$amount}" . PHP_EOL;
        
        $excl = $this->getExcludes();
        foreach ($excl as $pricepart) {
            $str .= "name={$pricepart->getName()} | operation={$pricepart->getOperation()} | absoluteAmount: {$pricepart->getAbsoluteAmount($amount)} | ";
            $str .= "{$amount} " . ($pricepart->getOperation(
                ) == PricePartOperation::ADD ? '+' : '-') . " {$pricepart->getAbsoluteAmount($amount)} = ";
            $amount = $pricepart->getNewAmount($amount);
            $str .= "{$amount}";
            $str .= PHP_EOL;
        }
        
        return $str;
    }
    
    
}
