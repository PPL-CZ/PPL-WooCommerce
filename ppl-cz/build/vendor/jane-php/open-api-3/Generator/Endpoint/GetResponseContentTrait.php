<?php

namespace PPLCZVendor\Jane\Component\OpenApi3\Generator\Endpoint;

use PPLCZVendor\Jane\Component\JsonSchemaRuntime\Reference;
use PPLCZVendor\Jane\Component\OpenApi3\Guesser\GuessClass;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\Response;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Normalizer\ResponseNormalizer;
use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
trait GetResponseContentTrait
{
    /**
     * @return string[]
     */
    public function getContentTypes(OperationGuess $operation, GuessClass $guessClass) : array
    {
        $produces = [];
        if ($operation->getOperation()->getResponses()) {
            foreach ($operation->getOperation()->getResponses() as $response) {
                if ($response instanceof Reference) {
                    [$_, $response] = $guessClass->resolve($response, Response::class);
                }
                if (\is_array($response)) {
                    $normalizer = new ResponseNormalizer();
                    $normalizer->setDenormalizer($this->denormalizer);
                    $response = $normalizer->denormalize($response, Response::class);
                }
                /** @var Response $response */
                if ($response->getContent()) {
                    foreach ($response->getContent() as $contentType => $content) {
                        $trimmedContentType = \trim($contentType);
                        if ($trimmedContentType !== '' && !\in_array($trimmedContentType, $produces)) {
                            $produces[] = $trimmedContentType;
                        }
                    }
                }
            }
            if ($operation->getOperation()->getResponses()->getDefault()) {
                $response = $operation->getOperation()->getResponses()->getDefault();
                if ($response instanceof Reference) {
                    [$_, $response] = $guessClass->resolve($response, Response::class);
                }
                /** @var Response $response */
                if ($response->getContent()) {
                    foreach ($response->getContent() as $contentType => $content) {
                        $trimmedContentType = \trim($contentType);
                        if ($trimmedContentType !== '' && !\in_array($trimmedContentType, $produces)) {
                            $produces[] = $trimmedContentType;
                        }
                    }
                }
            }
        }
        return $produces;
    }
}
