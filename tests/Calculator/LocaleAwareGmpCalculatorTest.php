<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use function setlocale;

use const LC_ALL;

/** @covers \Money\Calculator\GmpCalculator */
final class LocaleAwareGmpCalculatorTest extends GmpCalculatorTest
{
    public function setUp(): void
    {
        parent::setUp();

        setlocale(LC_ALL, 'ru_RU.UTF-8');
    }
}
