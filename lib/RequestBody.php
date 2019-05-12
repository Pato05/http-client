<?php

namespace Amp\Artax;

use Concurrent\Stream\ReadableStream;

/**
 * An interface for generating HTTP message bodies + headers.
 */
interface RequestBody
{
    /**
     * Retrieve a key-value array of headers to add to the outbound request.
     *
     * The resolved promise value must be a key-value array mapping header fields to values.
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Create the HTTP message body to be sent.
     *
     * Further calls MUST return a new stream to make it possible to resend bodies on redirects.
     *
     * @return ReadableStream
     *
     * @throws HttpException In case the stream can't be created
     */
    public function createBodyStream(): ReadableStream;

    /**
     * Retrieve the HTTP message body length. If not available, return -1.
     *
     * @return int
     */
    public function getBodyLength(): int;
}
