<?php

use ByJG\SmsClient\HydratePhone;
use PHPUnit\Framework\TestCase;

class HydratePhoneTest extends TestCase
{
    /**
     * @dataProvider usDataProvider
     */
    public function testUSNumber($source, $expected)
    {
        $phone = HydratePhone::phone($source)
            ->withPlusPrefix()
            ->withUSCountryCode()
            ->validateUSNumber()
            ->hydrate();
        $this->assertEquals($expected, $phone);

        $phone = HydratePhone::hydrateUsNumber($source);
        $this->assertEquals($expected, $phone);
    }

    public function usDataProvider()
    {
        return [
            ['+1(234)567-8900', '+12345678900' ],
            ['(234)567-8900', '+12345678900' ],
            ['+12345678900', '+12345678900' ],
            ['+2345678900', '+12345678900' ],
            ['12345678900', '+12345678900' ],
            ['2345678900', '+12345678900' ],
        ];
    }

    /**
     * @dataProvider usDataProvider2
     */
    public function testUSNumber2($source, $expected)
    {
        $phone = HydratePhone::phone($source)
            ->withNoPlusPrefix()
            ->withUSCountryCode()
            ->validateUSNumber()
            ->hydrate();
        $this->assertEquals($expected, $phone);
    }

    public function usDataProvider2()
    {
        return [
            ['+1(234)567-8900', '12345678900' ],
            ['(234)567-8900', '12345678900' ],
            ['+12345678900', '12345678900' ],
            ['+2345678900', '12345678900' ],
            ['12345678900', '12345678900' ],
            ['2345678900', '12345678900' ],
        ];
    }

    /**
     * @dataProvider usDataProvider3
     */
    public function testUSInvalid($source)
    {
        $this->expectException(InvalidArgumentException::class);

        $phone = HydratePhone::phone($source)
            ->validateUSNumber();
    }

    public function usDataProvider3()
    {
        return [
            ['11345678900'],
            ['92345678900'],
            ['1234567890'],
            ['123456789000'],
        ];
    }

    /**
     * @dataProvider brazilDataProvider
     */
    public function testBrazilNumber($source, $expected)
    {
        $phone = HydratePhone::phone($source)
            ->withPlusPrefix()
            ->withBrazilCountryCode()
            ->validateBrazilNumber()
            ->hydrate();
        $this->assertEquals($expected, $phone);

        $phone = HydratePhone::hydrateBrazilNumber($source);
        $this->assertEquals($expected, $phone);
    }

    public function brazilDataProvider()
    {
        return [
            ['+55(21)91234-5678', '+5521912345678' ],
            ['55(21)91234-5678', '+5521912345678' ],
            ['+5521912345678', '+5521912345678' ],
            ['5521912345678', '+5521912345678' ],
            ['21912345678', '+5521912345678' ],
            ['+21912345678', '+5521912345678' ],
        ];
    }

    /**
     * @dataProvider brazilDataProvider2
     */
    public function testBrazilNumber2($source, $expected)
    {
        $phone = HydratePhone::phone($source)
            ->withNoPlusPrefix()
            ->withBrazilCountryCode()
            ->validateBrazilNumber()
            ->hydrate();
        $this->assertEquals($expected, $phone);
    }

    public function brazilDataProvider2()
    {
        return [
            ['+(55)2191234-5678', '5521912345678' ],
            ['(55)2191234-5678', '5521912345678' ],
            ['+5521912345678', '5521912345678' ],
            ['5521912345678', '5521912345678' ],
            ['21912345678', '5521912345678' ],
            ['+21912345678', '5521912345678' ],
        ];
    }

    /**
     * @dataProvider brazilDataProvider3
     */
    public function testBrazilNumberInvalid($source)
    {
        $this->expectException(InvalidArgumentException::class);

        $phone = HydratePhone::phone($source)
            ->validateBrazilNumber();
    }

    public function brazilDataProvider3()
    {
        return [
            ['+55(21)91234-56789' ],
            ['55(21)91234-567' ],
            ['+55219123456789' ],
            ['552191234567' ],
            ['+552191234567' ],
            ['55219123456789' ],
            ['2191234567' ],
            ['+2191234567' ],
        ];
    }
}