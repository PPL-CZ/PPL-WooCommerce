<?php

namespace PPLCZVendor\Jane\Component\OpenApi3\Generator\Endpoint;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\Jane\Component\JsonSchema\Tools\InflectorTrait;
use PPLCZVendor\Jane\Component\JsonSchemaRuntime\Reference;
use PPLCZVendor\Jane\Component\OpenApi3\Generator\EndpointGenerator;
use PPLCZVendor\Jane\Component\OpenApi3\Generator\Parameter\NonBodyParameterGenerator;
use PPLCZVendor\Jane\Component\OpenApi3\Generator\RequestBodyGenerator;
use PPLCZVendor\Jane\Component\OpenApi3\Guesser\GuessClass;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\Parameter;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\RequestBody;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\Schema;
use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLCZVendor\PhpParser\Comment\Doc;
use PPLCZVendor\PhpParser\Node;
use PPLCZVendor\PhpParser\Node\Expr;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Stmt;
trait GetConstructorTrait
{
    use GetResponseContentTrait;
    use InflectorTrait;
    public function getConstructor(OperationGuess $operation, Context $context, GuessClass $guessClass, NonBodyParameterGenerator $nonBodyParameterGenerator, RequestBodyGenerator $requestBodyGenerator) : array
    {
        $pathParams = [];
        $bodyParam = null;
        $bodyDoc = null;
        $bodyAssign = null;
        $pathParamsDoc = [];
        $queryParamsDoc = [];
        $headerParamsDoc = [];
        $methodStatements = [];
        $pathProperties = [];
        $contentTypes = $this->getContentTypes($operation, $guessClass);
        foreach ($operation->getParameters() as $key => $parameter) {
            if ($parameter instanceof Reference) {
                $parameter = $guessClass->resolveParameter($parameter);
            }
            if (!$parameter instanceof \stdClass && $parameter->getSchema() instanceof Reference) {
                [$_, $schema] = $guessClass->resolve($parameter->getSchema(), Schema::class);
                $parameter->setSchema($schema);
            }
            if ($parameter instanceof Parameter && EndpointGenerator::IN_PATH === $parameter->getIn()) {
                $pathParams[] = $nonBodyParameterGenerator->generateMethodParameter($parameter, $context, $operation->getReference() . '/parameters/' . $key);
                $pathParamsDoc[] = $nonBodyParameterGenerator->generateMethodDocParameter($parameter, $context, $operation->getReference() . '/parameters/' . $key);
                $methodStatements[] = new Stmt\Expression(new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('this'), $parameter->getName()), new Expr\Variable($this->getInflector()->camelize($parameter->getName()))));
                $pathProperties[] = new Stmt\Property(Stmt\Class_::MODIFIER_PROTECTED, [new Stmt\PropertyProperty(new Name($parameter->getName()))]);
            }
            if ($parameter instanceof Parameter && EndpointGenerator::IN_QUERY === $parameter->getIn()) {
                $queryParamsDoc[] = $nonBodyParameterGenerator->generateOptionDocParameter($parameter);
            }
            if ($parameter instanceof Parameter && EndpointGenerator::IN_HEADER === $parameter->getIn()) {
                $headerParamsDoc[] = $nonBodyParameterGenerator->generateOptionDocParameter($parameter);
            }
        }
        if (($requestBody = $operation->getOperation()->getRequestBody()) instanceof RequestBody && null !== $requestBody->getContent()) {
            $bodyParam = $requestBodyGenerator->generateMethodParameter($requestBody, $operation->getReference() . '/requestBody', $context);
            $bodyDoc = $requestBodyGenerator->generateMethodDocParameter($requestBody, $operation->getReference() . '/requestBody', $context);
            $bodyAssign = new Stmt\Expression(new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('this'), 'body'), new Expr\Variable('requestBody')));
        }
        if (\count($contentTypes) > 1) {
            $pathProperties[] = new Stmt\Property(Stmt\Class_::MODIFIER_PROTECTED, [new Stmt\PropertyProperty(new Name('accept'))], []);
        }
        $methodStatements = \array_merge($methodStatements, $bodyAssign !== null ? [$bodyAssign] : [], \count($queryParamsDoc) > 0 ? [new Stmt\Expression(new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('this'), 'queryParameters'), new Expr\Variable('queryParameters')))] : [], \count($headerParamsDoc) > 0 ? [new Stmt\Expression(new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('this'), 'headerParameters'), new Expr\Variable('headerParameters')))] : [], \count($contentTypes) > 1 ? [new Stmt\Expression(new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('this'), 'accept'), new Expr\Variable('accept')))] : []);
        if (\count($methodStatements) === 0) {
            return [null, [], '/**', []];
        }
        $methodParams = \array_merge($pathParams, $bodyParam ? [$bodyParam] : [], \count($queryParamsDoc) > 0 ? [new Node\Param(new Expr\Variable('queryParameters'), new Expr\Array_(), new Name('array'))] : [], \count($headerParamsDoc) > 0 ? [new Node\Param(new Expr\Variable('headerParameters'), new Expr\Array_(), new Name('array'))] : [], \count($contentTypes) > 1 ? [new Node\Param(new Expr\Variable('accept'), new Expr\Array_(), new Name('array'))] : []);
        $methodDocumentations = \array_merge($pathParamsDoc, $bodyDoc ? [$bodyDoc] : [], \count($queryParamsDoc) > 0 ? \array_merge([' * @param array $queryParameters {'], $queryParamsDoc, [' * }']) : [], \count($headerParamsDoc) > 0 ? \array_merge([' * @param array $headerParameters {'], $headerParamsDoc, [' * }']) : [], \count($contentTypes) > 1 ? [' * @param array $accept Accept content header ' . \implode('|', $this->getContentTypes($operation, $guessClass))] : []);
        $methodParamsDoc = <<<EOD
/**
 * {$operation->getOperation()->getDescription()}
 *

EOD
 . \implode("\n", $methodDocumentations);
        return [new Stmt\ClassMethod('__construct', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'params' => $methodParams, 'stmts' => $methodStatements], ['comments' => [new Doc($methodParamsDoc . "\n */")]]), $methodParams, $methodParamsDoc, $pathProperties];
    }
}
