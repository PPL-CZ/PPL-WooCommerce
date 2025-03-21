<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Naming;

use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
interface OperationNamingInterface
{
    public function getFunctionName(OperationGuess $operation) : string;
    public function getEndpointName(OperationGuess $operation) : string;
}
