<?php

namespace Tests;

use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\BrazilianPhoneFormat;
use ByJG\SmsClient\PhoneFormat\PhoneFormat;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HydratePhoneTest extends TestCase
{
    /**
     * @dataProvider dataProviderWithPlusAndCountry
     */
    public function testHydrateNumberWithPlusAndCountry($source, PhoneFormat $phoneFormat, $expected, $expectedFormat)
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

    public function dataProviderWithPlusAndCountry()
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

    /**
     * @dataProvider dataProviderWithCountry
     */
    public function testNumberWithCountry($source, PhoneFormat $phoneFormat, $expected, $expectedFormat)
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

    public function dataProviderWithCountry()
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

    /**
     * @dataProvider dataProviderOnlyNumber
     */
    public function testOnlyNumber($source, PhoneFormat $phoneFormat, $expected, $expectedFormat)
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

    public function dataProviderOnlyNumber()
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

    /**
     * @dataProvider dateProviderInvalidPhone
     */
    public function testInvalidPhone($source, PhoneFormat $phoneFormat)
    {
        $this->expectException(InvalidArgumentException::class);

        $phone = Phone::phone($source, $phoneFormat)
            ->validate();
    }

    /**
     * @dataProvider dateProviderInvalidPhone
     */
    public function testInvalidPhone2($source, PhoneFormat $phoneFormat)
    {
        $validate = Phone::phone($source, $phoneFormat)
            ->validate(throwException: false);

        $this->assertFalse($validate);
    }

    public function dateProviderInvalidPhone()
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