<?php

namespace Jmikola\React\MongoDB;

use React\Dns\Resolver\Resolver;
use React\Dns\Resolver\Factory as DnsFactory;
use React\EventLoop\LoopInterface;
use React\SocketClient\Connector;
use React\SocketClient\SecureConnector;
use React\Stream\Stream;

class ConnectionFactory
{
    private $loop;
    private $connector;
    private $secureConnector;

    public function __construct(LoopInterface $loop, Resolver $resolver = null)
    {
        if (null === $resolver) {
            $factory = new DnsFactory();
            $resolver = $factory->create('8.8.8.8', $loop);
        }

        $this->loop = $loop;
        $this->connector = new Connector($loop, $resolver);
        $this->secureConnector = new SecureConnector($this->connector, $loop);
    }

    public function create($host, $port, array $options = null)
    {
        $host = (string) $host;
        $port = (integer) $port;

        $connector = empty($options['ssl']) ? $this->connector : $this->secureConnector;

        $promise = $connector->create($host, $port)->then(function (Stream $stream) {
            return new Connection($stream);
        });

        // TODO: Perform auth if username/password are set

        return $promise;
    }
}
