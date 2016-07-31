<?php

namespace Money\Currencies;

use Money\Currencies;
use Money\CurrenciesSpecification;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;

/**
 * List of supported ISO 4217 currency codes and names.
 *
 * @author Mathias Verraes
 */
final class ISOCurrencies implements Currencies, CurrenciesSpecification
{
    /**
     * List of known currencies.
     *
     * @var array
     */
    private static $currencies;

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        if (null === self::$currencies) {
            self::$currencies = $this->loadCurrencies();
        }

        return isset(self::$currencies[$currency->getCode()]);
    }

    /**
     * {@inheritdoc}
     */
    public function specify(Currency $currency)
    {
        if (null === self::$currencies) {
            self::$currencies = $this->loadCurrencies();
        }

        if (!isset(self::$currencies[$currency->getCode()])) {
            throw new UnknownCurrencyException('Cannot find ISO currency '.$currency->getCode());
        }

        $specification = (new Specification(
            self::$currencies[$currency->getCode()]['alphabeticCode'],
            self::$currencies[$currency->getCode()]['minorUnit']
        ))
            ->withName(self::$currencies[$currency->getCode()]['currency'])
            ->withEntity(self::$currencies[$currency->getCode()]['entity'])
            ->withNumericCode(self::$currencies[$currency->getCode()]['numericCode']);

        return $specification;
    }

    /**
     * @return array
     */
    private function loadCurrencies()
    {
        $file = __DIR__.'/../../vendor/moneyphp/iso-currencies/resources/current.php';

        if (file_exists($file)) {
            return require $file;
        }

        throw new \RuntimeException('Failed to load currency ISO codes.');
    }
}
