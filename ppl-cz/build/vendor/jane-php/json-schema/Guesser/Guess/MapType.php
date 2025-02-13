<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLCZVendor\PhpParser\Node\Expr;
use PPLCZVendor\PhpParser\Node\Name;
class MapType extends ArrayType
{
    public function __construct(object $object, Type $itemType)
    {
        parent::__construct($object, $itemType, 'object');
        $this->itemType = $itemType;
    }
    public function getTypeHint(string $namespace)
    {
        return new Name('iterable');
    }
    public function getDocTypeHint(string $namespace)
    {
        return new Name(\sprintf('array<string, %s>', $this->getItemType()->getDocTypeHint($namespace)));
    }
    protected function createArrayValueStatement() : Expr
    {
        return new Expr\New_(new Name('\\ArrayObject'), [new Expr\Array_(), new Expr\ClassConstFetch(new Name('\\ArrayObject'), 'ARRAY_AS_PROPS')]);
    }
    protected function createNormalizationArrayValueStatement() : Expr
    {
        return new Expr\Array_();
    }
    protected function createLoopKeyStatement(Context $context) : Expr
    {
        return new Expr\Variable($context->getUniqueVariableName('key'));
    }
    protected function createLoopOutputAssignement(Expr $valuesVar, $loopKeyVar) : Expr
    {
        return new Expr\ArrayDimFetch($valuesVar, $loopKeyVar);
    }
    protected function createNormalizationLoopOutputAssignement(Expr $valuesVar, $loopKeyVar) : Expr
    {
        return new Expr\ArrayDimFetch($valuesVar, $loopKeyVar);
    }
}
