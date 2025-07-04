<?php

declare (strict_types=1);
namespace PPLCZVendor\Http\Client\Common;

use PPLCZVendor\Psr\Http\Message\RequestInterface;
use PPLCZVendor\Psr\Http\Message\ResponseInterface;
/**
 * Emulates an HTTP Client in an HTTP Async Client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait HttpClientEmulator
{
    /**
     * @see HttpClient::sendRequest
     */
    public function sendRequest(RequestInterface $request) : ResponseInterface
    {
        $promise = $this->sendAsyncRequest($request);
        return $promise->wait();
    }
    /**
     * @see HttpAsyncClient::sendAsyncRequest
     */
    public abstract function sendAsyncRequest(RequestInterface $request);
}
