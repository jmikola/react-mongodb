<?php

namespace Jmikola\React\MongoDB;

use React\Dns\Resolver\Factory as DnsFactory;
use React\Dns\Resolver\Resolver;
use React\EventLoop\LoopInterface;
use React\SocketClient\Connector;
use React\SocketClient\SecureConnector;
use React\Stream\Stream;

class ConnectionFactory
{
    /** @var LoopInterface */
    private $loop;
    /** @var Connector */
    private $connector;
    /** @var SecureConnector */
    private $secureConnector;

    const GOOGLE_DNS = '8.8.8.8';

    /**
     * ConnectionFactory constructor.
     * @param LoopInterface $loop
     * @param Resolver|null $resolver
     */
    public function __construct(LoopInterface $loop, Resolver $resolver = null)
    {
        if (null === $resolver) {
            $factory = new DnsFactory();
            $resolver = $factory->create(self::GOOGLE_DNS, $loop);
        }

        $this->loop = $loop;
        $this->connector = new Connector($loop, $resolver);
        $this->secureConnector = new SecureConnector($this->connector, $loop);
    }

    /**
     * Create connection to mongodb
     *
     * @param string $host
     * @param int $port
     * @param array|null $options
     * @return \React\Promise\PromiseInterface
     */
    public function create($host, $port, array $options = null)
    {
        $host = (string)$host;
        $port = (integer)$port;

        $connector = empty($options['ssl']) ? $this->connector : $this->secureConnector;

        $promise = $connector->create($host, $port)->then(function (Stream $stream) {
            return new Connection($stream);
        });

        // TODO: Perform auth if username/password are set

        return $promise;
    }
}
