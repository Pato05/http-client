<?php

// Usage -----------------------------------------------
// Default  (10 x 100    requests): php examples/8-benchmark.php
// Infinite (10 x 100    requests): php examples/8-benchmark.php 0
// Custom   (10 x $count requests): php examples/8-benchmark.php $count

use Amp\Artax\Client;
use Amp\Artax\Request;
use Concurrent\Task;
use function Concurrent\all;

require __DIR__ . '/../vendor/autoload.php';

$count = (int) ($argv[1] ?? 1000);

$client = new Amp\Artax\DefaultClient;
$client->setOption(Client::OP_TRANSFER_TIMEOUT, 5000);

do {
    $awaitables = [];

    for ($i = 0; $i < 10; $i++) {
        $awaitables[] = Task::async(function (int $count) use ($client) {
            for ($i = 0; $i < $count; $i++) {
                $response = $client->request(Request::fromString('http://localhost:1337/'));
                $response->getBody()->buffer();
            }
        }, $count === 0 ? 100 : $count);
    }

    print "Waiting...";
    Task::await(all($awaitables));
    print " Done." . PHP_EOL;

    gc_collect_cycles();
    gc_mem_caches();

    print "Memory: " . (memory_get_usage(true) / 1000) . PHP_EOL;
} while ($count === 0);
