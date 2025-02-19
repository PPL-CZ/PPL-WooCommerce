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

use PPLCZVendor\Humbug\PhpScoper\Symbol\EnrichedReflector;
use PPLCZVendor\PhpParser\Node;
use PPLCZVendor\PhpParser\Node\Name;
use PPLCZVendor\PhpParser\Node\Stmt\Namespace_;
use PPLCZVendor\PhpParser\NodeVisitorAbstract;
/**
 * Prefixes the relevant namespaces.
 *
 * ```
 * namespace Foo;
 * ```
 *
 * =>
 *
 * ```
 * namespace Humbug\Foo;
 * ```
 *
 * @private
 */
final class NamespaceStmtPrefixer extends NodeVisitorAbstract
{
    private string $prefix;
    private EnrichedReflector $enrichedReflector;
    private NamespaceStmtCollection $namespaceStatements;
    public function __construct(string $prefix, EnrichedReflector $enrichedReflector, NamespaceStmtCollection $namespaceStatements)
    {
        $this->prefix = $prefix;
        $this->enrichedReflector = $enrichedReflector;
        $this->namespaceStatements = $namespaceStatements;
    }
    public function enterNode(Node $node) : Node
    {
        return $node instanceof Namespace_ ? $this->prefixNamespaceStmt($node) : $node;
    }
    private function prefixNamespaceStmt(Namespace_ $namespace) : Node
    {
        if ($this->shouldPrefixStmt($namespace)) {
            self::prefixStmt($namespace, $this->prefix);
        }
        $this->namespaceStatements->add($namespace);
        return $namespace;
    }
    private function shouldPrefixStmt(Namespace_ $namespace) : bool
    {
        $name = $namespace->name;
        if ($this->enrichedReflector->isExcludedNamespace((string) $name)) {
            return \false;
        }
        $nameFirstPart = null === $name ? '' : $name->getFirst();
        return $this->prefix !== $nameFirstPart;
    }
    private static function prefixStmt(Namespace_ $namespace, string $prefix) : void
    {
        $originalName = $namespace->name;
        $namespace->name = Name::concat($prefix, $originalName);
        NamespaceManipulator::setOriginalName($namespace, $originalName);
    }
}
