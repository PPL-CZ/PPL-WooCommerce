<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\Object_;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ObjectCheckTrait;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorInterface;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLCZVendor\Symfony\Component\Validator\Constraints\Count;
class MaxPropertiesValidator implements ValidatorInterface
{
    use ObjectCheckTrait;
    public function supports($object) : bool
    {
        return $this->checkObject($object) && ((\is_array($object->getType()) ? \in_array('object', $object->getType()) : 'object' === $object->getType()) || null === $object->getType() && \is_array($object->getProperties()) && \count($object->getProperties()) > 0) && \is_int($object->getMaxProperties());
    }
    /**
     * @param JsonSchema          $object
     * @param ClassGuess|Property $guess
     */
    public function guess($object, string $name, $guess) : void
    {
        $guess->addValidatorGuess(new ValidatorGuess(Count::class, ['max' => $object->getMaxProperties(), 'maxMessage' => 'This object has too much properties. It should have {{ limit }} properties or less.']));
    }
}
