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
namespace PPLCZVendor\Humbug\PhpScoper\Scoper;

use PPLCZVendor\Humbug\PhpScoper\Throwable\Exception\ParsingException;
interface Scoper
{
    /**
     * Scope AKA. apply the given prefix to the file in the appropriate way.
     *
     * @param string     $filePath  File to scope
     * @param string     $contents  File contents
     *
     * @throws ParsingException
     *
     * @return string Contents of the file with the prefix applied
     */
    public function scope(string $filePath, string $contents) : string;
}
