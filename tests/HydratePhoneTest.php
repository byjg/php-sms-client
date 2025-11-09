<?php

namespace Tests;

use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\BrazilianPhoneFormat;
use ByJG\SmsClient\PhoneFormat\PhoneFormat;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HydratePhoneTest extends TestCase
{
    #[DataProvider('dataProviderWithPlusAndCountry')]
    public function testHydrateNumberWithPlusAndCountry($source, PhoneFormat $phoneFormat, $expected, $expectedFormat): void
    {
        $phone = Phone::phone($source, $phoneFormat)
            ->withPlusPrefix()
            ->withCountryCode()
            ->hydrate();
        $this->assertEquals($expected, $phone);

        $phone = Phone::phone($source, $phoneFormat)
            ->withPlusPrefix()
            ->withCountryCode()
            ->format();
        $this->assertEquals($expectedFormat, $phone);

        $validate = Phone::phone($source, $phoneFormat)
            ->withPlusPrefix()
            ->withCountryCode()
            ->validate(throwException: false);
        $this->assertTrue($validate);
    }

    /**
     * @return (BrazilianPhoneFormat|USPhoneFormat|string)[][]
     *
     * @psalm-return list{list{'+1(234)567-8900', USPhoneFormat, '+12345678900', '+1(234)567-8900'}, list{'(234)567-8900', USPhoneFormat, '+12345678900', '+1(234)567-8900'}, list{'+12345678900', USPhoneFormat, '+12345678900', '+1(234)567-8900'}, list{'+2345678900', USPhoneFormat, '+12345678900', '+1(234)567-8900'}, list{'12345678900', USPhoneFormat, '+12345678900', '+1(234)567-8900'}, list{'2345678900', USPhoneFormat, '+12345678900', '+1(234)567-8900'}, list{'+55(21)91234-5678', BrazilianPhoneFormat, '+5521912345678', '+55(21)91234-5678'}, list{'55(21)91234-5678', BrazilianPhoneFormat, '+5521912345678', '+55(21)91234-5678'}, list{'+5521912345678', BrazilianPhoneFormat, '+5521912345678', '+55(21)91234-5678'}, list{'5521912345678', BrazilianPhoneFormat, '+5521912345678', '+55(21)91234-5678'}, list{'21912345678', BrazilianPhoneFormat, '+5521912345678', '+55(21)91234-5678'}, list{'+21912345678', BrazilianPhoneFormat, '+5521912345678', '+55(21)91234-5678'}}
     */
    public static function dataProviderWithPlusAndCountry(): array
    {
        return [
            ['+1(234)567-8900', new USPhoneFormat(), '+12345678900', '+1(234)567-8900' ],
            ['(234)567-8900', new USPhoneFormat(), '+12345678900', '+1(234)567-8900' ],
            ['+12345678900', new USPhoneFormat(), '+12345678900', '+1(234)567-8900' ],
            ['+2345678900', new USPhoneFormat(), '+12345678900', '+1(234)567-8900' ],
            ['12345678900', new USPhoneFormat(), '+12345678900', '+1(234)567-8900' ],
            ['2345678900', new USPhoneFormat(), '+12345678900', '+1(234)567-8900' ],
            ['+55(21)91234-5678', new BrazilianPhoneFormat(), '+5521912345678', '+55(21)91234-5678' ],
            ['55(21)91234-5678', new BrazilianPhoneFormat(), '+5521912345678', '+55(21)91234-5678' ],
            ['+5521912345678', new BrazilianPhoneFormat(), '+5521912345678', '+55(21)91234-5678' ],
            ['5521912345678', new BrazilianPhoneFormat(), '+5521912345678', '+55(21)91234-5678' ],
            ['21912345678', new BrazilianPhoneFormat(), '+5521912345678', '+55(21)91234-5678' ],
            ['+21912345678', new BrazilianPhoneFormat(), '+5521912345678', '+55(21)91234-5678' ],
        ];
    }

    #[DataProvider('dataProviderWithCountry')]
    public function testNumberWithCountry($source, PhoneFormat $phoneFormat, $expected, $expectedFormat): void
    {
        $phone = Phone::phone($source, $phoneFormat)
            ->withNoPlusPrefix()
            ->hydrate();
        $this->assertEquals($expected, $phone);

        $phone = Phone::phone($source, $phoneFormat)
            ->withNoPlusPrefix()
            ->format();
        $this->assertEquals($expectedFormat, $phone);

        $validate = Phone::phone($source, $phoneFormat)
            ->withNoPlusPrefix()
            ->validate(throwException: false);
        $this->assertTrue($validate);
    }

    /**
     * @return (BrazilianPhoneFormat|USPhoneFormat|string)[][]
     *
     * @psalm-return list{list{'+1(234)567-8900', USPhoneFormat, '12345678900', '1(234)567-8900'}, list{'(234)567-8900', USPhoneFormat, '12345678900', '1(234)567-8900'}, list{'+12345678900', USPhoneFormat, '12345678900', '1(234)567-8900'}, list{'+2345678900', USPhoneFormat, '12345678900', '1(234)567-8900'}, list{'12345678900', USPhoneFormat, '12345678900', '1(234)567-8900'}, list{'2345678900', USPhoneFormat, '12345678900', '1(234)567-8900'}, list{'+(55)2191234-5678', BrazilianPhoneFormat, '5521912345678', '55(21)91234-5678'}, list{'(55)2191234-5678', BrazilianPhoneFormat, '5521912345678', '55(21)91234-5678'}, list{'+5521912345678', BrazilianPhoneFormat, '5521912345678', '55(21)91234-5678'}, list{'5521912345678', BrazilianPhoneFormat, '5521912345678', '55(21)91234-5678'}, list{'21912345678', BrazilianPhoneFormat, '5521912345678', '55(21)91234-5678'}, list{'+21912345678', BrazilianPhoneFormat, '5521912345678', '55(21)91234-5678'}}
     */
    public static function dataProviderWithCountry(): array
    {
        return [
            ['+1(234)567-8900', new USPhoneFormat(), '12345678900', '1(234)567-8900' ],
            ['(234)567-8900', new USPhoneFormat(), '12345678900', '1(234)567-8900' ],
            ['+12345678900', new USPhoneFormat(), '12345678900', '1(234)567-8900' ],
            ['+2345678900', new USPhoneFormat(), '12345678900', '1(234)567-8900' ],
            ['12345678900', new USPhoneFormat(), '12345678900', '1(234)567-8900' ],
            ['2345678900', new USPhoneFormat(), '12345678900', '1(234)567-8900' ],
            ['+(55)2191234-5678', new BrazilianPhoneFormat(), '5521912345678', '55(21)91234-5678' ],
            ['(55)2191234-5678', new BrazilianPhoneFormat(), '5521912345678', '55(21)91234-5678' ],
            ['+5521912345678', new BrazilianPhoneFormat(), '5521912345678', '55(21)91234-5678' ],
            ['5521912345678', new BrazilianPhoneFormat(), '5521912345678', '55(21)91234-5678' ],
            ['21912345678', new BrazilianPhoneFormat(), '5521912345678', '55(21)91234-5678' ],
            ['+21912345678', new BrazilianPhoneFormat(), '5521912345678', '55(21)91234-5678' ],
        ];
    }

    #[DataProvider('dataProviderOnlyNumber')]
    public function testOnlyNumber($source, PhoneFormat $phoneFormat, $expected, $expectedFormat): void
    {
        $phone = Phone::phone($source, $phoneFormat)
            ->withNoPlusPrefix()
            ->withNoCountryCode()
            ->hydrate();
        $this->assertEquals($expected, $phone);

        $phone = Phone::phone($source, $phoneFormat)
            ->withNoPlusPrefix()
            ->withNoCountryCode()
            ->format();
        $this->assertEquals($expectedFormat, $phone);

        $validate = Phone::phone($source, $phoneFormat)
            ->withNoPlusPrefix()
            ->withNoCountryCode()
            ->validate(throwException: false);
        $this->assertTrue($validate);
    }

    /**
     * @return (BrazilianPhoneFormat|USPhoneFormat|string)[][]
     *
     * @psalm-return list{list{'+1(234)567-8900', USPhoneFormat, '2345678900', '(234)567-8900'}, list{'(234)567-8900', USPhoneFormat, '2345678900', '(234)567-8900'}, list{'+12345678900', USPhoneFormat, '2345678900', '(234)567-8900'}, list{'+2345678900', USPhoneFormat, '2345678900', '(234)567-8900'}, list{'12345678900', USPhoneFormat, '2345678900', '(234)567-8900'}, list{'2345678900', USPhoneFormat, '2345678900', '(234)567-8900'}, list{'+(55)2191234-5678', BrazilianPhoneFormat, '21912345678', '(21)91234-5678'}, list{'(55)2191234-5678', BrazilianPhoneFormat, '21912345678', '(21)91234-5678'}, list{'+5521912345678', BrazilianPhoneFormat, '21912345678', '(21)91234-5678'}, list{'5521912345678', BrazilianPhoneFormat, '21912345678', '(21)91234-5678'}, list{'21912345678', BrazilianPhoneFormat, '21912345678', '(21)91234-5678'}, list{'+21912345678', BrazilianPhoneFormat, '21912345678', '(21)91234-5678'}}
     */
    public static function dataProviderOnlyNumber(): array
    {
        return [
            ['+1(234)567-8900', new USPhoneFormat(), '2345678900', '(234)567-8900' ],
            ['(234)567-8900', new USPhoneFormat(), '2345678900', '(234)567-8900' ],
            ['+12345678900', new USPhoneFormat(), '2345678900', '(234)567-8900' ],
            ['+2345678900', new USPhoneFormat(), '2345678900', '(234)567-8900' ],
            ['12345678900', new USPhoneFormat(), '2345678900', '(234)567-8900' ],
            ['2345678900', new USPhoneFormat(), '2345678900', '(234)567-8900' ],
            ['+(55)2191234-5678', new BrazilianPhoneFormat(), '21912345678', '(21)91234-5678' ],
            ['(55)2191234-5678', new BrazilianPhoneFormat(), '21912345678', '(21)91234-5678' ],
            ['+5521912345678', new BrazilianPhoneFormat(), '21912345678', '(21)91234-5678' ],
            ['5521912345678', new BrazilianPhoneFormat(), '21912345678', '(21)91234-5678' ],
            ['21912345678', new BrazilianPhoneFormat(), '21912345678', '(21)91234-5678' ],
            ['+21912345678', new BrazilianPhoneFormat(), '21912345678', '(21)91234-5678' ],
        ];
    }

    #[DataProvider('dateProviderInvalidPhone')]
    public function testInvalidPhone($source, PhoneFormat $phoneFormat): void
    {
        $this->expectException(InvalidArgumentException::class);

        $phone = Phone::phone($source, $phoneFormat)
            ->validate();
    }

    #[DataProvider('dateProviderInvalidPhone')]
    public function testInvalidPhone2($source, PhoneFormat $phoneFormat): void
    {
        $validate = Phone::phone($source, $phoneFormat)
            ->validate(throwException: false);

        $this->assertFalse($validate);
    }

    /**
     * @return (BrazilianPhoneFormat|USPhoneFormat|string)[][]
     *
     * @psalm-return list{list{'11345678900', USPhoneFormat}, list{'92345678900', USPhoneFormat}, list{'1234567890', USPhoneFormat}, list{'123456789000', USPhoneFormat}, list{'+55(21)91234-56789', BrazilianPhoneFormat}, list{'55(21)91234-567', BrazilianPhoneFormat}, list{'+55219123456789', BrazilianPhoneFormat}, list{'552191234567', BrazilianPhoneFormat}, list{'+552191234567', BrazilianPhoneFormat}, list{'55219123456789', BrazilianPhoneFormat}, list{'2191234567', BrazilianPhoneFormat}, list{'+2191234567', BrazilianPhoneFormat}}
     */
    public static function dateProviderInvalidPhone(): array
    {
        return [
            ['11345678900', new USPhoneFormat()],
            ['92345678900', new USPhoneFormat()],
            ['1234567890', new USPhoneFormat()],
            ['123456789000', new USPhoneFormat()],
            ['+55(21)91234-56789', new BrazilianPhoneFormat()],
            ['55(21)91234-567', new BrazilianPhoneFormat()],
            ['+55219123456789', new BrazilianPhoneFormat()],
            ['552191234567', new BrazilianPhoneFormat()],
            ['+552191234567', new BrazilianPhoneFormat()],
            ['55219123456789', new BrazilianPhoneFormat()],
            ['2191234567', new BrazilianPhoneFormat()],
            ['+2191234567', new BrazilianPhoneFormat()],
        ];
    }
}