<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Exception\InvalidClassException;
use ByJG\SmsClient\Exception\ProtocolNotRegisteredException;
use ByJG\SmsClient\ReturnObject;
use ByJG\Util\Uri;

class ProviderFactory
{
    private static $config = [];

    private static $services = [];

    /**
     * @param string $protocol
     * @param string $class
     * @return void
     */
    public static function registerProvider($class)
    {
        if (!in_array(ProviderInterface::class, class_implements($class))) {
            throw new InvalidClassException('Class not implements ProviderInterface!');
        }

        $protocolList = $class::schema();
        foreach ((array)$protocolList as $item) {
            self::$config[$item] = $class;
        }
    }

    /**
     * @param Uri|string $connection
     * @return ProviderInterface
     */
    public static function create($connection): ProviderInterface
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
        $object = new $class($uri);
        $object->setUp($uri);

        return $object;
    }

    public static function registerServices($connection, $validPrefixes = null)
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

    public static function createAndSend($to, $message): ReturnObject
    {
        $provider = null;
        foreach (self::$services as $prefix => $connection) {
            if (preg_match('/^\+?' . trim($prefix, "+") . '/', $to)) {
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