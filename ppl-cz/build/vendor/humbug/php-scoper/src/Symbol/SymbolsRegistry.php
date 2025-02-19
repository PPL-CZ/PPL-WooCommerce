<?php

declare (strict_types=1);
/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLCZVendor\Humbug\PhpScoper\Symbol;

use Countable;
use PPLCZVendor\PhpParser\Node\Name\FullyQualified;
use function array_values;
use function count;
final class SymbolsRegistry implements Countable
{
    /**
     * @var array<string, array{string, string}>
     */
    private array $recordedFunctions = [];
    /**
     * @var array<string, array{string, string}>
     */
    private array $recordedClasses = [];
    public static function createFromRegistries(array $symbolsRegistries) : self
    {
        $symbolsRegistry = new SymbolsRegistry();
        foreach ($symbolsRegistries as $symbolsRegistryToMerge) {
            $symbolsRegistry->merge($symbolsRegistryToMerge);
        }
        return $symbolsRegistry;
    }
    public function merge(self $symbolsRegistry) : void
    {
        foreach ($symbolsRegistry->getRecordedFunctions() as [$original, $alias]) {
            $this->recordedFunctions[$original] = [$original, $alias];
        }
        foreach ($symbolsRegistry->getRecordedClasses() as [$original, $alias]) {
            $this->recordedClasses[$original] = [$original, $alias];
        }
    }
    public function recordFunction(FullyQualified $original, FullyQualified $alias) : void
    {
        $this->recordedFunctions[(string) $original] = [(string) $original, (string) $alias];
    }
    /**
     * @return list<array{string, string}>
     */
    public function getRecordedFunctions() : array
    {
        return array_values($this->recordedFunctions);
    }
    public function recordClass(FullyQualified $original, FullyQualified $alias) : void
    {
        $this->recordedClasses[(string) $original] = [(string) $original, (string) $alias];
    }
    /**
     * @return list<array{string, string}>
     */
    public function getRecordedClasses() : array
    {
        return array_values($this->recordedClasses);
    }
    public function count() : int
    {
        return count($this->recordedFunctions) + count($this->recordedClasses);
    }
}
