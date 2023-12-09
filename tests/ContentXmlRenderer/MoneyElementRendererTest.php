<?php

declare(strict_types=1);

namespace Tests\ContentXmlRenderer;

use AqBanking\ContentXmlRenderer\MoneyElementRenderer;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\ContentXmlRenderer\MoneyElementRenderer
 */
class MoneyElementRendererTest extends TestCase
{
    /**
     * @test
     */
    public function can_render_0_euro(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::EUR(0);
        $this->assertEquals($expected, $sut->render('0/100', 'EUR'));
    }

    /**
     * @test
     */
    public function can_render_1_euro(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::EUR(100);
        $this->assertEquals($expected, $sut->render('100/100', 'EUR'));
    }

    /**
     * @test
     */
    public function can_render_minus_1_euro(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::EUR(-100);
        $this->assertEquals($expected, $sut->render('-100/100', 'EUR'));
    }

    /**
     * @test
     */
    public function can_render_10_euros(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::EUR(1000);
        $this->assertEquals($expected, $sut->render('1000/100', 'EUR'));
    }

    /**
     * @test
     */
    public function can_render_100_euros(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::EUR(10000);
        $this->assertEquals($expected, $sut->render('10000/100', 'EUR'));
    }

    /**
     * @test
     */
    public function can_render_1_swiss_franc(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::CHF(100);
        $this->assertEquals($expected, $sut->render('100/100', 'CHF'));
    }

    /**
     * @test
     * @see https://github.com/janunger/aqbanking-php/issues/1
     */
    public function can_render_with_10_as_divisor(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::EUR(67890);
        $this->assertEquals($expected, $sut->render('6789/10', 'EUR'));
    }

    /**
     * @test
     */
    public function can_render_with_1000_as_divisor(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::EUR(6789);
        $this->assertEquals($expected, $sut->render('67890/1000', 'EUR'));
    }

    /**
     * @test
     */
    public function can_render_with_1_as_divisor(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::EUR(678900);
        $this->assertEquals($expected, $sut->render('6789/1', 'EUR'));
    }

    /**
     * @test
     * @see https://github.com/janunger/aqbanking-php/issues/1
     */
    public function can_render_without_an_explicit_divisor(): void
    {
        $sut = new MoneyElementRenderer();

        $expected = Money::EUR(678900);
        $this->assertEquals($expected, $sut->render('6789', 'EUR'));
    }

    /**
     * @test
     */
    public function throws_exception_on_unexpected_divisor(): void
    {
        $sut = new MoneyElementRenderer();

        $this->expectException('\AqBanking\RuntimeException');
        $sut->render('200/200', 'EUR');
    }

    /**
     * @test
     */
    public function throws_exception_if_amount_is_biassed_on_transformation(): void
    {
        $sut = new MoneyElementRenderer();

        $this->expectException('\AqBanking\RuntimeException');
        $this->expectExceptionMessage('Biassed rendering result');
        $sut->render('1234/1000', 'EUR');
    }

    /**
     * @test
     */
    public function throws_exception_on_unknown_currency(): void
    {
        $sut = new MoneyElementRenderer();

        $this->expectException('\AqBanking\RuntimeException');
        $sut->render('1/100', '___');
    }

    /**
     * @test
     */
    public function throws_exception_on_invalid_amount(): void
    {
        $sut = new MoneyElementRenderer();

        $this->expectException('\AqBanking\RuntimeException');
        $sut->render('/100', 'EUR');
    }
}
