<?php

namespace Tests\Provider;

use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;
use ByJG\SmsClient\Provider\ByJGSmsProvider;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\WebRequest\Exception\MessageException;
use PHPUnit\Framework\TestCase;

final class ByJGSmsProviderTest extends TestCase
{
    public function testSendSMS(): void
    {
        ProviderFactory::registerProvider(ByJGSmsProviderMock::class);

        ProviderFactory::registerServices("byjg://user:password@default", "+55");

        $response = ProviderFactory::createAndSend("+5521912345678", (new \ByJG\SmsClient\Message("This is a test message")));
        $this->assertTrue($response->isSent());
    }

    public function testSendSMSError(): void
    {
        ProviderFactory::registerProvider(ByJGSmsProviderMock::class);

        ProviderFactory::registerServices("byjg://user:password@default", "+1");

        $this->expectException(MessageException::class);
        $response = ProviderFactory::createAndSend(Phone::phone("+12129121234", new USPhoneFormat()), (new \ByJG\SmsClient\Message("This is a test message")));
        $this->assertTrue($response->isSent());
    }
}
