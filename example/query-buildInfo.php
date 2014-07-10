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
        $query = new Query('admin.$cmd', array('buildInfo' => 1), null, 0, 1);

        $connection->on('message', function(Reply $reply) {
            printf("# received reply with message length: %d\n", $reply->getMessageLength());
            // Note: this only works because a single document is returned
            var_dump(bson_decode($reply->getDocumentsData()));
        });

        $connection->on('close', function() {
            printf("# connection closed!\n");
        });

        $connection->send($query)->then(
            function(Reply $reply) {
                printf("# query executed successfully!\n");
            },
            function (Exception $e) {
                printf("# query error: %s\n", $e->getMessage());
                printf("%s\n", $e->getTraceAsString());
                exit(1);
            }
        );
    },
    function (Exception $e) {
        printf("# connection error: %s\n", $e->getMessage());
        printf("%s\n", $e->getTraceAsString());
        exit(1);
    }
);

$loop->run();
