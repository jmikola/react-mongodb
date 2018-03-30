<?php

use Jmikola\React\MongoDB\Connection;
use Jmikola\React\MongoDB\ConnectionFactory;
use Jmikola\React\MongoDB\Protocol\Reply;

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$factory = new ConnectionFactory($loop);

$connection = $factory->create('127.0.0.1', 27017)->then(
    function (Connection $connection) {
        $connection->on('close', function () {
            printf("# connection closed\n");
        });

        $connection->on('error', function (Exception $e) {
            printf("# connection error: %s\n", $e->getMessage());
            printf("%s\n", $e->getTraceAsString());
        });

        $connection->on('message', function (Reply $reply) {
            printf("# received reply of size: %d\n", $reply->getMessageLength());
        });

//        $message = new \Jmikola\React\MongoDB\Protocol\Insert(
//            'test.foo',
//            [
//                (object) ['foo' => 'value'],
//                (object) ['bar' => 'value']
//            ]
//        );
        $message = new \Jmikola\React\MongoDB\Protocol\Insert('test.foo', (object) ['foo' => 'bar']);

        $connection->send($message)->then(
            function (Reply $reply) {
                printf("# query executed successfully!\n");
                var_dump($reply);
            },
            function (Exception $e) {
                printf("# query error: %s\n", $e->getMessage());
                printf("%s\n", $e->getTraceAsString());
            }
        );

        $connection->end();
    },
    function (Exception $e) {
        printf("# connection error: %s\n", $e->getMessage());
        printf("%s\n", $e->getTraceAsString());
    }
);

$loop->run();
