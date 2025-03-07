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
namespace PPLCZVendor\Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt;

use PPLCZVendor\Humbug\PhpScoper\PhpParser\Node\FullyQualifiedFactory;
use PPLCZVendor\Humbug\PhpScoper\PhpParser\NodeVisitor\ParentNodeAppender;
use PPLCZVendor\Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use PPLCZVendor\Humbug\PhpScoper\PhpParser\UnexpectedParsingScenario;
use PPLCZVendor\Humbug\PhpScoper\Symbol\EnrichedReflector;
use PPLCZVendor\Humbug\PhpScoper\Symbol\SymbolsRegistry;
use InvalidArgumentException;
use PPLCZVendor\PhpParser\Node;
use PPLCZVendor\PhpParser\Node\Arg;
use PPLCZVendor\PhpParser\Node\Expr\FuncCall;
use PPLCZVendor\PhpParser\Node\Identifier;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Name\FullyQualified;
use PPLCZVendor\PhpParser\Node\Scalar\String_;
use PPLCZVendor\PhpParser\Node\Stmt\Function_;
use PPLCZVendor\PhpParser\NodeVisitorAbstract;
/**
 * Records the user functions registered in the global namespace which have been whitelisted and whitelisted functions.
 *
 * @private
 */
final class FunctionIdentifierRecorder extends NodeVisitorAbstract
{
    private string $prefix;
    private IdentifierResolver $identifierResolver;
    private SymbolsRegistry $symbolsRegistry;
    private EnrichedReflector $enrichedReflector;
    public function __construct(string $prefix, IdentifierResolver $identifierResolver, SymbolsRegistry $symbolsRegistry, EnrichedReflector $enrichedReflector)
    {
        $this->prefix = $prefix;
        $this->identifierResolver = $identifierResolver;
        $this->symbolsRegistry = $symbolsRegistry;
        $this->enrichedReflector = $enrichedReflector;
    }
    public function enterNode(Node $node) : Node
    {
        if (!($node instanceof Identifier || $node instanceof Name || $node instanceof String_) || !ParentNodeAppender::hasParent($node)) {
            return $node;
        }
        $resolvedName = $this->retrieveResolvedName($node);
        if (null !== $resolvedName && $this->enrichedReflector->isExposedFunction($resolvedName->toString())) {
            $this->symbolsRegistry->recordFunction($resolvedName, FullyQualifiedFactory::concat($this->prefix, $resolvedName));
        }
        return $node;
    }
    private function retrieveResolvedName(Node $node) : ?FullyQualified
    {
        if ($node instanceof Identifier) {
            return $this->retrieveResolvedNameForIdentifier($node);
        }
        if ($node instanceof Name) {
            return $this->retrieveResolvedNameForFuncCall($node);
        }
        if ($node instanceof String_) {
            return $this->retrieveResolvedNameForString($node);
        }
        throw UnexpectedParsingScenario::create();
    }
    private function retrieveResolvedNameForIdentifier(Identifier $identifier) : ?FullyQualified
    {
        $parent = ParentNodeAppender::getParent($identifier);
        if (!$parent instanceof Function_ || $identifier === $parent->returnType) {
            return null;
        }
        $resolvedName = $this->identifierResolver->resolveIdentifier($identifier);
        return $resolvedName instanceof FullyQualified ? $resolvedName : null;
    }
    private function retrieveResolvedNameForFuncCall(Name $name) : ?FullyQualified
    {
        $parent = ParentNodeAppender::getParent($name);
        if (!$parent instanceof FuncCall) {
            return null;
        }
        return $name instanceof FullyQualified ? $name : null;
    }
    private function retrieveResolvedNameForString(String_ $string) : ?FullyQualified
    {
        $stringParent = ParentNodeAppender::getParent($string);
        if (!$stringParent instanceof Arg) {
            return null;
        }
        $argParent = ParentNodeAppender::getParent($stringParent);
        if (!$argParent instanceof FuncCall) {
            return null;
        }
        if (!self::isFunctionExistsCall($argParent)) {
            return null;
        }
        $resolvedName = $this->identifierResolver->resolveString($string);
        return $resolvedName instanceof FullyQualified ? $resolvedName : null;
    }
    private static function isFunctionExistsCall(FuncCall $node) : bool
    {
        $name = $node->name;
        return $name instanceof Name && $name->isFullyQualified() && $name->toString() === 'function_exists';
    }
}
