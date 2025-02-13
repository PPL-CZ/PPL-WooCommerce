<?php

namespace PPLCZVendor\Jane\Component\OpenApi3\Generator;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\GeneratorInterface;
use PPLCZVendor\Jane\Component\OpenApi3\Generator\Parameter\NonBodyParameterGenerator;
use PPLCZVendor\Jane\Component\OpenApi3\Generator\RequestBodyContent\DefaultBodyContentGenerator;
use PPLCZVendor\Jane\Component\OpenApi3\Generator\RequestBodyContent\FormBodyContentGenerator;
use PPLCZVendor\Jane\Component\OpenApi3\Generator\RequestBodyContent\JsonBodyContentGenerator;
use PPLCZVendor\Jane\Component\OpenApiCommon\Generator\ExceptionGenerator;
use PPLCZVendor\Jane\Component\OpenApiCommon\Generator\OperationGenerator;
use PPLCZVendor\Jane\Component\OpenApiCommon\Naming\ChainOperationNaming;
use PPLCZVendor\Jane\Component\OpenApiCommon\Naming\OperationIdNaming;
use PPLCZVendor\Jane\Component\OpenApiCommon\Naming\OperationUrlNaming;
use PPLCZVendor\PhpParser\ParserFactory;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
class GeneratorFactory
{
    public static function build(DenormalizerInterface $serializer, string $endpointGeneratorClass) : GeneratorInterface
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        $nonBodyParameter = new NonBodyParameterGenerator($serializer, $parser);
        $exceptionGenerator = new ExceptionGenerator();
        $operationNaming = new ChainOperationNaming([new OperationIdNaming(), new OperationUrlNaming()]);
        $defaultContentGenerator = new DefaultBodyContentGenerator($serializer);
        $requestBodyGenerator = new RequestBodyGenerator($defaultContentGenerator);
        $requestBodyGenerator->addRequestBodyGenerator(JsonBodyContentGenerator::JSON_TYPES, new JsonBodyContentGenerator($serializer));
        $requestBodyGenerator->addRequestBodyGenerator(['application/x-www-form-urlencoded', 'multipart/form-data'], new FormBodyContentGenerator($serializer));
        if (!\class_exists($endpointGeneratorClass)) {
            throw new \InvalidArgumentException(\sprintf('Unknown generator class %s', $endpointGeneratorClass));
        }
        $endpointGenerator = new $endpointGeneratorClass($operationNaming, $nonBodyParameter, $serializer, $exceptionGenerator, $requestBodyGenerator);
        $operationGenerator = new OperationGenerator($endpointGenerator);
        return new ClientGenerator($operationGenerator, $operationNaming);
    }
}
