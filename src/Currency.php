<?php

namespace Money;

/**
 * Currency Value Object.
 *
 * Holds Currency specific data.
 *
 * @author Mathias Verraes
 */
final class Currency implements \JsonSerializable
{
    const DEFAULT_NUMBER_OF_SUBUNITS = 2;

    /**
     * Currency code.
     *
     * @var string
     */
    private $code;

    /**
     * @var int
     */
    private $numberOfSubUnits;

    /**
     * @param string $code
     * @param int    $numberOfSubUnits
     */
    public function __construct($code, $numberOfSubUnits = self::DEFAULT_NUMBER_OF_SUBUNITS)
    {
        if (!is_string($code)) {
            throw new \InvalidArgumentException('Currency code should be string');
        }

        $this->code = $code;
        $this->numberOfSubUnits = $numberOfSubUnits;
    }

    /**
     * Returns the currency code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Checks whether this currency is the same as an other.
     *
     * @param Currency $other
     *
     * @return bool
     */
    public function equals(Currency $other)
    {
        return $this->code === $other->code;
    }

    /**
     * Checks whether this currency is available in the passed context.
     *
     * @param Currencies $currencies
     *
     * @return bool
     */
    public function isAvailableWithin(Currencies $currencies)
    {
        return $currencies->contains($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCode();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getNumberOfSubUnits()
    {
        return $this->numberOfSubUnits;
    }
}
