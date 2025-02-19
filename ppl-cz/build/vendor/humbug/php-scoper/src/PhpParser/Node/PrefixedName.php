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
namespace PPLCZVendor\Humbug\PhpScoper\PhpParser\Node;

use PPLCZVendor\PhpParser\Node\Name\FullyQualified;
// TODO:review
final class PrefixedName extends FullyQualified
{
    private FullyQualified $prefixedName;
    private FullyQualified $originalName;
    public function __construct(FullyQualified $prefixedName, FullyQualified $originalName, array $attributes = [])
    {
        parent::__construct($prefixedName, $attributes);
        $this->prefixedName = new FullyQualified($prefixedName, $attributes);
        $this->originalName = new FullyQualified($originalName, $attributes);
    }
    public function getPrefixedName() : FullyQualified
    {
        return $this->prefixedName;
    }
    public function getOriginalName() : FullyQualified
    {
        return $this->originalName;
    }
}
