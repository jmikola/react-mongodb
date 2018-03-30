<?php

use Jmikola\React\MongoDB\Connection;
use Jmikola\React\MongoDB\ConnectionFactory;
use Jmikola\React\MongoDB\Protocol\Query;
use Jmikola\React\MongoDB\Protocol\Reply;

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$factory = new ConnectionFactory($loop);

$connection = $factory->create('127.0.0.1', 27017)->then(
    function (Connection $connection) {
        $query = new Query('admin.$cmd', ['buildInfo' => 1], null, 0, 1);

        $connection->send($query)->then(
            function (Reply $reply) {
                printf("# query executed successfully!\n");
                foreach ($reply as $document) {
                    var_dump($document);
                }
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
