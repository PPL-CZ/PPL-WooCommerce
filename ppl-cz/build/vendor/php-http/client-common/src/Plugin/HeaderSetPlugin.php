<?php

declare (strict_types=1);
namespace PPLCZVendor\Http\Client\Common\Plugin;

use PPLCZVendor\Http\Client\Common\Plugin;
use PPLCZVendor\Http\Promise\Promise;
use PPLCZVendor\Psr\Http\Message\RequestInterface;
/**
 * Set headers on the request.
 *
 * If the header does not exist it wil be set, if the header already exists it will be replaced.
 *
 * @author Soufiane Ghzal <sghzal@gmail.com>
 */
final class HeaderSetPlugin implements Plugin
{
    /**
     * @var array
     */
    private $headers;
    /**
     * @param array $headers Hashmap of header name to header value
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }
    public function handleRequest(RequestInterface $request, callable $next, callable $first) : Promise
    {
        foreach ($this->headers as $header => $headerValue) {
            $request = $request->withHeader($header, $headerValue);
        }
        return $next($request);
    }
}
