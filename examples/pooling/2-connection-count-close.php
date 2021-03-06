<?php

use Amp\Http\Client\Connection\UnlimitedConnectionPool;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\HttpException;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Amp\Loop;

require __DIR__ . '/../.helper/functions.php';

Loop::run(static function () use ($argv) {
    try {
        // There's no need to create a custom pool here, we just need it to access the statistics.
        $pool = new UnlimitedConnectionPool;

        $client = (new HttpClientBuilder)->usingPool($pool)->followRedirects(0)->build();

        // connection: close in the request disables keep-alive for HTTP/1, it's ignored on HTTP/2
        $firstRequest = new Request($argv[1] ?? 'https://httpbin.org/user-agent');
        $firstRequest->setHeader('connection', 'close');

        /** @var Response $firstResponse */
        $firstResponse = yield $client->request($firstRequest);

        dumpResponseTrace($firstResponse);
        dumpResponseBodyPreview(yield $firstResponse->getBody()->buffer());

        $secondRequest = new Request($argv[1] ?? 'https://httpbin.org/user-agent');
        $secondRequest->setHeader('connection', 'close');

        /** @var Response $secondResponse */
        $secondResponse = yield $client->request($secondRequest);

        dumpResponseTrace($secondResponse);
        dumpResponseBodyPreview(yield $secondResponse->getBody()->buffer());

        print "Total connection attempts: " . $pool->getTotalConnectionAttempts() . "\r\n";
        print "Total stream requests: " . $pool->getTotalStreamRequests() . "\r\n";
        print "Currently open connections: " . $pool->getOpenConnectionCount() . "\r\n";
    } catch (HttpException $error) {
        echo $error;
    }
});
