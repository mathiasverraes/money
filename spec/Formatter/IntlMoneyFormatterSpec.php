<?php

namespace spec\Money\Formatter;

use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

class IntlMoneyFormatterSpec extends ObjectBehavior
{
    function let(\NumberFormatter $numberFormatter)
    {
        $this->beConstructedWith($numberFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Formatter\IntlMoneyFormatter');
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_returns_subunits(\NumberFormatter $numberFormatter)
    {
        $numberFormatter->getAttribute(\NumberFormatter::FRACTION_DIGITS)->willReturn(2);

        $money = new Money(100, new Currency('EUR'));

        $this->subunits($money)->shouldReturn('1.00');
    }

    function it_formats_money(\NumberFormatter $numberFormatter)
    {
        $numberFormatter->formatCurrency('1.00', 'EUR')->willReturn('€1.00');
        $numberFormatter->getAttribute(\NumberFormatter::FRACTION_DIGITS)->willReturn(2);

        $money = new Money(100, new Currency('EUR'));

        $this->format($money)->shouldReturn('€1.00');
    }
}
