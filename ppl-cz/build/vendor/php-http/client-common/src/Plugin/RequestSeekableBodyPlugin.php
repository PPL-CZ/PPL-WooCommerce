<?php

declare (strict_types=1);
namespace PPLCZVendor\Http\Client\Common\Plugin;

use PPLCZVendor\Http\Message\Stream\BufferedStream;
use PPLCZVendor\Http\Promise\Promise;
use PPLCZVendor\Psr\Http\Message\RequestInterface;
/**
 * Allow body used in request to be always seekable.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class RequestSeekableBodyPlugin extends SeekableBodyPlugin
{
    public function handleRequest(RequestInterface $request, callable $next, callable $first) : Promise
    {
        if (!$request->getBody()->isSeekable()) {
            $request = $request->withBody(new BufferedStream($request->getBody(), $this->useFileBuffer, $this->memoryBufferSize));
        }
        return $next($request);
    }
}
