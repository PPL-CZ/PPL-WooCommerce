<?php

declare (strict_types=1);
namespace PPLCZVendor\Http\Client\Common\Plugin;

use PPLCZVendor\Http\Client\Common\Plugin;
use PPLCZVendor\Http\Message\Encoding\ChunkStream;
use PPLCZVendor\Http\Promise\Promise;
use PPLCZVendor\Psr\Http\Message\RequestInterface;
/**
 * Allow to set the correct content length header on the request or to transfer it as a chunk if not possible.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class ContentLengthPlugin implements Plugin
{
    public function handleRequest(RequestInterface $request, callable $next, callable $first) : Promise
    {
        if (!$request->hasHeader('Content-Length')) {
            $stream = $request->getBody();
            // Cannot determine the size so we use a chunk stream
            if (null === $stream->getSize()) {
                $stream = new ChunkStream($stream);
                $request = $request->withBody($stream);
                $request = $request->withAddedHeader('Transfer-Encoding', 'chunked');
            } else {
                $request = $request->withHeader('Content-Length', (string) $stream->getSize());
            }
        }
        return $next($request);
    }
}
