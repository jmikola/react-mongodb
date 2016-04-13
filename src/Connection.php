<?php

namespace Jmikola\React\MongoDB;

use Evenement\EventEmitter;
use Jmikola\React\MongoDB\Protocol\Reply;
use Jmikola\React\MongoDB\Protocol\RequestInterface;
use Jmikola\React\MongoDB\Protocol\ResponseParser;
use React\Stream\Stream;
use Exception;
use RuntimeException;
use SplQueue;
use UnderflowException;
use UnexpectedValueException;

class Connection extends EventEmitter
{
    /** @var bool */
    private $ending;
    /** @var ResponseParser  */
    private $responseParser;
    /** @var int */
    private $requestId;
    /** @var SplQueue  */
    private $requestQueue;
    /** @var Stream  */
    private $stream;

    /**
     * Connection constructor.
     * @param Stream $stream
     */
    public function __construct(Stream $stream)
    {
        $this->requestQueue = new SplQueue();
        $this->responseParser = new ResponseParser();
        $this->stream = $stream;

        $stream->on('data', function($data) {
            try {
                $replies = $this->responseParser->pushAndGetParsed($data);

                foreach ($replies as $reply) {
                    $this->handleReply($reply);
                }
            } catch (RuntimeException $e) {
                $this->emit('error', [$e]);
                $this->close();
                return;
            }
        });

        $stream->on('close', function() {
            $this->close();
            $this->emit('close');
        });

        $stream->on('error', function(Exception $e, Stream $stream) {
            $this->emit('error', [$e, $stream]);
            $this->close();
        });
    }

    public function close()
    {
        $this->ending = true;

        $this->stream->close();

        // reject all remaining requests in the queue
        while ( ! $this->requestQueue->isEmpty()) {
            $request = $this->requestQueue->dequeue();
            $request->reject(new RuntimeException('Connection closed'));
        }
    }

    public function end()
    {
        $this->ending = true;

        if ($this->requestQueue->isEmpty()) {
            $this->close();
        }
    }

    /**
     * @param Reply $reply
     */
    public function handleReply(Reply $reply)
    {
        $this->emit('message', [$reply, $this]);

        if ($this->requestQueue->isEmpty()) {
            throw new UnderflowException('Unexpected reply received; request queue is empty');
        }

        $request = $this->requestQueue->dequeue();

        if ( ! $reply->isResponseTo($request->getRequestId())) {
            throw new UnexpectedValueException(sprintf('Request ID (%d) does not match reply (%d)', $request->getRequestId(), $reply->getResponseTo()));
        }

        $request->handleReply($reply);

        if ($this->ending && $this->requestQueue->isEmpty()) {
            $this->close();
        }
    }

    /**
     * @param RequestInterface $requestMessage
     * @return \React\Promise\PromiseInterface
     */
    public function send(RequestInterface $requestMessage)
    {
        if ($this->ending) {
            return \React\Promise\reject(new RuntimeException('Connection closed'));
        }

        // TODO: Ensure generated request IDs are unique per host until rollover
        $requestId = ++$this->requestId;

        $this->stream->write($requestMessage->getMessageData($requestId));

        $deferredRequest = new Request($requestId);

        $this->requestQueue->enqueue($deferredRequest);

        return $deferredRequest->promise();
    }
}
