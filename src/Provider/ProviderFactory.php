<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Exception\InvalidClassException;
use ByJG\SmsClient\Exception\ProtocolNotRegisteredException;
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\ReturnObject;
use ByJG\Util\Uri;

final class ProviderFactory
{
    private static array $config = [];

    private static array $services = [];

    /**
     * @param string $class
     * @return void
     * @throws InvalidClassException
     */
    public static function registerProvider(string $class): void
    {
        $implements = class_implements($class);
        if ($implements === false || !in_array(ProviderInterface::class, $implements)) {
            throw new InvalidClassException('Class not implements ProviderInterface!');
        }

        /** @var ProviderInterface $class */
        $protocolList = $class::schema();
        foreach ($protocolList as $item) {
            self::$config[$item] = $class;
        }
    }

    /**
     * @param Uri|string $connection
     * @return ProviderInterface
     * @throws ProtocolNotRegisteredException
     */
    public static function create(Uri|string $connection): ProviderInterface
    {
        if ($connection instanceof Uri) {
            $uri = $connection;
        } else {
            $uri = new Uri($connection);
        }

        if (!isset(self::$config[$uri->getScheme()])) {
            throw new ProtocolNotRegisteredException('Protocol not found/registered!');
        }

        $class = self::$config[$uri->getScheme()];
        /** @var ProviderInterface $object */
        $object = new $class($uri);
        $object->setUp($uri);

        return $object;
    }

    /**
     * @throws ProtocolNotRegisteredException
     */
    public static function registerServices(string|Uri $connection, string|null $validPrefixes = null): void
    {
        if ($connection instanceof Uri) {
            $uri = $connection;
        } else {
            $uri = new Uri($connection);
        }

        if (!isset(self::$config[$uri->getScheme()])) {
            throw new ProtocolNotRegisteredException('Protocol not found/registered!');
        }

        if (is_null($validPrefixes)) {
            $validPrefixes = 'default';
        }

        foreach ((array)$validPrefixes as $prefix) {
            self::$services[$prefix] = $connection;
        }
    }

    /**
     * @throws ProtocolNotRegisteredException
     */
    public static function createAndSend(string|Phone $to, \ByJG\SmsClient\Message $message): ReturnObject
    {
        $provider = null;
        foreach (self::$services as $prefix => $connection) {
            if (is_string($to) && preg_match('/^\+?' . trim($prefix, "+") . '/', $to)) {
                $provider = self::create($connection);
                break;
            } else if ($to instanceof Phone && $prefix == "+" . $to->getPhoneFormat()->getCountryCode()) {
                $provider = self::create($connection);
                break;
            }
        }

        if (empty($provider) && !isset(self::$services['default'])) {
            throw new ProtocolNotRegisteredException('Prefix not found/registered!');
        } else if (empty($provider)) {
            $provider = self::create(self::$services['default']);
        }

        return $provider->send($to, $message);
    }

}