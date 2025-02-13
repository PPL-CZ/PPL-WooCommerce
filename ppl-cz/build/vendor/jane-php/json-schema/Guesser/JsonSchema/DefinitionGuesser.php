<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareTrait;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ClassGuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Registry;
class DefinitionGuesser implements ChainGuesserAwareInterface, GuesserInterface, ClassGuesserInterface
{
    use ChainGuesserAwareTrait;
    /**
     * {@inheritdoc}
     *
     * @param JsonSchema $object
     */
    public function guessClass($object, string $name, string $reference, Registry $registry) : void
    {
        /**
         * @var string
         * @var JsonSchema $definition
         */
        foreach ($object->getDefinitions() ?? [] as $key => $definition) {
            $this->chainGuesser->guessClass($definition, $key, $reference . '/definitions/' . $key, $registry);
        }
        /**
         * @var string
         * @var JsonSchema $definition
         */
        foreach ($object->getDollarDefs() ?? [] as $key => $definition) {
            $this->chainGuesser->guessClass($definition, $key, $reference . '/definitions/' . $key, $registry);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function supportObject($object) : bool
    {
        return $object instanceof JsonSchema && (null !== $object->getDefinitions() && \count($object->getDefinitions()) > 0 || null !== $object->getDollarDefs() && \count($object->getDollarDefs()) > 0);
    }
    protected function getSchemaClass() : string
    {
        return JsonSchema::class;
    }
}
