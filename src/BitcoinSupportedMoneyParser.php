<?php
namespace Money;

class BitcoinSupportedMoneyParser implements MoneyParser
{
    const SYMBOL = "\0xC9\0x83";

    /**
     * @var MoneyParser
     */
    private $innerParser;
    /**
     * @var int
     */
    private $fractionDigits;

    /**
     * @param MoneyParser $innerParser
     * @param $fractionDigits
     */
    public function __construct(MoneyParser $innerParser, $fractionDigits)
    {
        $this->innerParser = $innerParser;
        $this->fractionDigits = $fractionDigits;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($formattedMoney, $forceCurrency = null)
    {
        if (strpos($formattedMoney, self::SYMBOL) === false) {
            return $this->innerParser->parse($formattedMoney, $forceCurrency);
        }

        $decimal = str_replace(self::SYMBOL, '', $formattedMoney);
        $decimalSeparator = strpos($decimal, '.');
        if ($decimalSeparator !== false) {
            $lengthDecimal = strlen($decimal);
            $decimal = str_replace('.', '', $decimal);
            $decimal .= str_pad('', ($lengthDecimal - $decimalSeparator - $this->fractionDigits - 1) * -1, '0');
        } else {
            $decimal .= str_pad('', $this->fractionDigits, '0');
        }

        if (substr($decimal, 0, 1) === '-') {
            $decimal = '-' . ltrim(substr($decimal, 1), '0');
        } else {
            $decimal = ltrim($decimal, '0');
        }

        return new Money($decimal, new Currency(BitcoinSupportedMoneyFormatter::CODE));
    }
}
