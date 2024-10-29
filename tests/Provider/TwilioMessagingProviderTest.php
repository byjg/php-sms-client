<?php

namespace Tests\Provider;

use ByJG\SmsClient\Message;
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\WebRequest\Exception\MessageException;
use PHPUnit\Framework\TestCase;

class TwilioMessagingProviderTest extends TestCase
{
    public function testSendSMS()
    {
        ProviderFactory::registerProvider(TwilioMessagingProviderMock::class);

        ProviderFactory::registerServices("twilio://accoundId:authToken@default	", "+1");

        $response = ProviderFactory::createAndSend("+12129121234", ((new Message("This is a test message"))->withSender("+12129121235")));
        $this->assertTrue($response->isSent());
    }

//    public function testSendSMSError()
//    {
//        ProviderFactory::registerProvider(TwilioMessagingProviderMock::class);
//
//        ProviderFactory::registerServices("twilio://accoundId:authToken@default	", "+1");
//
//        $this->expectException(MessageException::class);
//        $response = ProviderFactory::createAndSend(Phone::phone("+12129121234", new USPhoneFormat()), (new Message("This is a test message")));
//        $this->assertTrue($response->isSent());
//    }
}
