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
namespace PPLCZVendor\Humbug\PhpScoper\PhpParser\NodeVisitor;

use PPLCZVendor\Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use PPLCZVendor\PhpParser\Node;
use PPLCZVendor\PhpParser\Node\Stmt\Class_;
use PPLCZVendor\PhpParser\Node\Stmt\Interface_;
use PPLCZVendor\PhpParser\NodeVisitorAbstract;
/**
 * In some contexts we need to resolve identifiers but they can no longer be
 * resolved on the fly. For those, we store the resolved identifier as an
 * attribute.
 *
 * @see ClassAliasStmtAppender
 *
 * @private
 */
final class IdentifierNameAppender extends NodeVisitorAbstract
{
    private IdentifierResolver $identifierResolver;
    public function __construct(IdentifierResolver $identifierResolver)
    {
        $this->identifierResolver = $identifierResolver;
    }
    public function enterNode(Node $node) : ?Node
    {
        if (!($node instanceof Class_ || $node instanceof Interface_)) {
            return null;
        }
        $name = $node->name;
        if (null === $name) {
            return null;
        }
        $resolvedName = $this->identifierResolver->resolveIdentifier($name);
        $name->setAttribute('resolvedName', $resolvedName);
        return null;
    }
}
