<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Generator;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLCZVendor\PhpParser\Comment;
use PPLCZVendor\PhpParser\Node;
use PPLCZVendor\PhpParser\Node\Arg;
use PPLCZVendor\PhpParser\Node\Expr;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Param;
use PPLCZVendor\PhpParser\Node\Stmt;
use PPLCZVendor\Psr\Http\Message\ResponseInterface;
class OperationGenerator
{
    protected $endpointGenerator;
    public function __construct(EndpointGeneratorInterface $endpointGenerator)
    {
        $this->endpointGenerator = $endpointGenerator;
    }
    protected function getReturnDoc(array $returnTypes, array $throwTypes) : string
    {
        return \implode('', \array_map(function ($value) {
            return ' * @throws ' . $value . "\n";
        }, $throwTypes)) . " *\n" . ' * @return ' . \implode('|', $returnTypes);
    }
    public function createOperation(string $name, OperationGuess $operation, Context $context) : Stmt\ClassMethod
    {
        /** @var Param[] $methodParams */
        [$endpointName, $methodParams, $methodDoc, $returnTypes, $throwTypes] = $this->endpointGenerator->createEndpointClass($operation, $context);
        $endpointArgs = [];
        // Make sure the $fetch param is in front of $accept header for backwards compatibility.
        $lastMethodParam = '';
        foreach ($methodParams as $param) {
            $endpointArgs[] = new Arg($param->var);
            $lastMethodParam = $param->var->name;
        }
        $methodDocSplit = \explode("\n", $methodDoc);
        $methodDocPosition = $lastMethodParam === 'accept' ? \count($methodDocSplit) - 1 : \count($methodDocSplit);
        \array_splice($methodDocSplit, $methodDocPosition, 0, [' * @param string $fetch Fetch mode to use (can be OBJECT or RESPONSE)']);
        $methodDocSplit[] = $this->getReturnDoc(\array_merge($returnTypes, ['\\' . ResponseInterface::class]), $throwTypes);
        $methodDocSplit[] = ' */';
        $documentation = \implode("\n", $methodDocSplit);
        $paramsPosition = $lastMethodParam === 'accept' ? \count($methodParams) - 1 : \count($methodParams);
        \array_splice($methodParams, $paramsPosition, 0, [new Param(new Node\Expr\Variable('fetch'), new Expr\ClassConstFetch(new Name('self'), 'FETCH_OBJECT'), new Name('string'))]);
        return new Stmt\ClassMethod($name, ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'params' => $methodParams, 'stmts' => [new Stmt\Return_(new Expr\MethodCall(new Expr\Variable('this'), 'executeEndpoint', [new Arg(new Expr\New_(new Name\FullyQualified($endpointName), $endpointArgs)), new Arg(new Expr\Variable('fetch'))]))]], ['comments' => [new Comment\Doc($documentation)]]);
    }
}
