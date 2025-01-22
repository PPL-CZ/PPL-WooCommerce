<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess as BaseClassGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Property;
class ClassGuess extends BaseClassGuess
{
    /** @var ParentClass */
    private $parentClass;
    public function getParentClass() : ?ParentClass
    {
        return $this->parentClass;
    }
    public function setParentClass(?ParentClass $parentClass) : void
    {
        $this->parentClass = $parentClass;
    }
    public function getLocalProperties() : array
    {
        if (null === $this->parentClass) {
            return $this->getProperties();
        }
        return \array_filter(
            // return only those properties which not present in parent class
            $this->getProperties(),
            function (Property $property) : bool {
                return null === $this->parentClass->getProperty($property->getName());
            }
        );
    }
}
