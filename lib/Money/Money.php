<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2013 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

class Money
{
    const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

    /**
     * @var int
     */
    private $amount;

    /** @var \Money\Currency */
    private $currency;

    /**
     * @var int
     */
    private $precision;

    /**
     * Create a Money instance
     * @param  integer $amount Amount, expressed in the smallest units of $currency (eg cents)
     * @param  \Money\Currency $currency
     * @param int $precision
     * @throws InvalidArgumentException
     */
    public function __construct($amount, Currency $currency, $precision = 2)
    {
        if (!is_int($amount)) {
            throw new InvalidArgumentException("The first parameter of Money must be an integer. It's the amount, expressed in the smallest units of currency (eg cents)");
        }
        if (!is_int($precision)) {
            throw new InvalidArgumentException("The second parameter of Money must be an integer. It's the precision, or the number of significant decimal places (eg 2, or 4)");
        }
        $this->amount = $amount;
        $this->currency = $currency;
        $this->precision = $precision;
    }

    /**
     * @param string $decimalAmount
     * @param Currency $currency
     * @param int $precision
     * @return Money
     */
    public static function fromDecimal($decimalAmount, Currency $currency, $precision = 2) {
        bcscale($precision);
        return new Money((int)(bcmul( bcpow(10, $precision) , $decimalAmount)), $currency, $precision);
    }

    /**
     * @return string
     */
    public function toDecimal()
    {
        bcscale($this->precision);
        return bcdiv($this->getAmount(), bcpow(10, $this->precision));
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->toDecimal().' '.$this->getCurrency()->getName();
    }

    /**
     * @param int $precision
     * @return Money
     */
    public function toPrecision($precision=null)
    {
        if($this->precision == $precision || $precision === null)
            return $this;

        return self::fromDecimal($this->toDecimal(), $this->currency, $precision);
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return Money
     */
    public function copy()
    {
        return new Money($this->amount, $this->currency, $this->precision);
    }


    /**
     * Convenience factory method for a Money object
     * For a IDE friendlier alternative, use MoneyFactory
     * @example $fiveDollar = Money::USD(500);
     * @param string $method
     * @param array $arguments
     * @return \Money\Money
     */
    public static function __callStatic($method, $arguments)
    {

        return  is_int(@$arguments[1]) ?
                new Money($arguments[0], new Currency($method), $arguments[1]) :
                new Money($arguments[0], new Currency($method));
    }

    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function isSameCurrency(Money $other)
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * @throws \Money\InvalidArgumentException
     */
    private function assertSameCurrency(Money $other)
    {
        if (!$this->isSameCurrency($other)) {
            throw new InvalidArgumentException('Different currencies');
        }
    }

    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function equals(Money $other)
    {
        return
            $this->isSameCurrency($other)
            && $this->amount == $other->amount;
    }

    /**
     * @param \Money\Money $other
     * @return int
     */
    public function compare(Money $other)
    {
        $this->assertSameCurrency($other);
        $otherAmount = $other->toPrecision($this->precision)->amount;
        if ($this->amount < $otherAmount) {
            return -1;
        } elseif ($this->amount == $otherAmount) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function greaterThan(Money $other)
    {
        return 1 == $this->compare($other);
    }
    
    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function greaterThanOrEqual(Money $other)
    {
        return 0 >= $this->compare($other);
    }

    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function lessThan(Money $other)
    {
        return -1 == $this->compare($other);
    }
    
    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function lessThanOrEqual(Money $other)
    {
        return 0 <= $this->compare($other);
    }

    /**
     * @deprecated Use getAmount() instead
     * @return int
     */
    public function getUnits()
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return \Money\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param \Money\Money $addend
     *@return \Money\Money 
     */
    public function add(Money $addend)
    {
        $this->assertSameCurrency($addend);

        return new self($this->amount + $addend->toPrecision($this->precision)->amount, $this->currency, $this->precision);
    }

    /**
     * @param \Money\Money $subtrahend
     * @return \Money\Money
     */
    public function subtract(Money $subtrahend)
    {
        $this->assertSameCurrency($subtrahend);

        return new self($this->amount - $subtrahend->toPrecision($this->precision)->amount, $this->currency, $this->precision);
    }

    /**
     * @param int|float $operand
     * @throws \Money\InvalidArgumentException
     */
    private function assertOperand($operand)
    {
        if (!is_int($operand) && !is_float($operand)) {
            throw new InvalidArgumentException('Operand should be an integer or a float');
        }

    }

    /**
     * @throws \Money\InvalidArgumentException
     */
    private function assertRoundingMode($rounding_mode)
    {
        if (!in_array($rounding_mode, array(self::ROUND_HALF_DOWN, self::ROUND_HALF_EVEN, self::ROUND_HALF_ODD, self::ROUND_HALF_UP))) {
            throw new InvalidArgumentException('Rounding mode should be Money::ROUND_HALF_DOWN | Money::ROUND_HALF_EVEN | Money::ROUND_HALF_ODD | Money::ROUND_HALF_UP');
        }
    }

    /**
     * @param $multiplier
     * @param int $rounding_mode
     * @return \Money\Money
     */
    public function multiply($multiplier, $rounding_mode = self::ROUND_HALF_UP)
    {
        $this->assertOperand($multiplier);
        $this->assertRoundingMode($rounding_mode);

        $product = (int) round($this->amount * $multiplier, 0, $rounding_mode);

        return new Money($product, $this->currency, $this->precision);
    }

    /**
     * @param $divisor
     * @param int $rounding_mode
     * @return \Money\Money
     */
    public function divide($divisor, $rounding_mode = self::ROUND_HALF_UP)
    {
        if ($divisor === 0 || $divisor === 0.0){
            throw new InvalidArgumentException('Division by zero');
        }

        $this->assertOperand($divisor, true);
        $this->assertRoundingMode($rounding_mode);

        $quotient = (int) round($this->amount / $divisor, 0, $rounding_mode);

        return new Money($quotient, $this->currency, $this->precision);
    }

    /**
     * Allocate the money according to a list of ratio's
     * The resulting array of money objects has the same keys of the ratios array
     * @param array $ratios List of ratio's
     * @param int $precision
     * @return \Money\Money[]
     */
    public function allocate(array $ratios, $precision = null)
    {
        $precision = $precision ? $precision : $this->precision;
        $amount = $remainder = $this->toPrecision($precision)->getAmount();

        $results = array();
        $total = array_sum($ratios);

        foreach ($ratios as $key => $ratio) {
            $share = (int) floor($amount * $ratio / $total);
            $results[$key] = new Money($share, $this->currency, $precision);;
            $remainder -= $share;
        }

        foreach($results as &$result) {
            if($remainder > 0) {
                $result->amount++;
                $remainder--;
            } else break;
        }

        return $results;
    }

    /** @return bool */
    public function isZero()
    {
        return $this->amount === 0;
    }

    /** @return bool */
    public function isPositive()
    {
        return $this->amount > 0;
    }

    /** @return bool */
    public function isNegative()
    {
        return $this->amount < 0;
    }

    /**
     * @param $string
     * @throws \Money\InvalidArgumentException
     * @return int
     */
    public static function stringToUnits( $string )
    {
        $sign = "(?P<sign>[-\+])?";
        $digits = "(?P<digits>\d*)";
        $separator = "(?P<separator>[.,])?";
        $decimals = "(?P<decimal1>\d)?(?P<decimal2>\d)?";
        $pattern = "/^".$sign.$digits.$separator.$decimals."$/";

        if (!preg_match($pattern, trim($string), $matches)) {
            throw new InvalidArgumentException("The value could not be parsed as money");
        }

        $units = $matches['sign'] == "-" ? "-" : "";
        $units .= $matches['digits'];
        $units .= isset($matches['decimal1']) ? $matches['decimal1'] : "0";
        $units .= isset($matches['decimal2']) ? $matches['decimal2'] : "0";

        return (int) $units;
    }
}
