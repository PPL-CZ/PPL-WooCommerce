<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\OpenApiSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\JsonSchema\CustomStringFormatGuesser as BaseCustomStringFormatGuesser;
class CustomStringFormatGuesser extends BaseCustomStringFormatGuesser
{
    use SchemaClassTrait;
    public function __construct(string $schemaClass, array $mapping)
    {
        parent::__construct($mapping);
        $this->schemaClass = $schemaClass;
    }
}
