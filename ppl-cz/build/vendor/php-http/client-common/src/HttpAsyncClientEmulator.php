<?php

declare (strict_types=1);
namespace PPLCZVendor\Http\Client\Common;

use PPLCZVendor\Http\Client\Exception;
use PPLCZVendor\Http\Client\Promise;
use PPLCZVendor\Psr\Http\Message\RequestInterface;
use PPLCZVendor\Psr\Http\Message\ResponseInterface;
/**
 * Emulates an HTTP Async Client in an HTTP Client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait HttpAsyncClientEmulator
{
    /**
     * @see HttpClient::sendRequest
     */
    public abstract function sendRequest(RequestInterface $request) : ResponseInterface;
    /**
     * @see HttpAsyncClient::sendAsyncRequest
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        try {
            return new Promise\HttpFulfilledPromise($this->sendRequest($request));
        } catch (Exception $e) {
            return new Promise\HttpRejectedPromise($e);
        }
    }
}
