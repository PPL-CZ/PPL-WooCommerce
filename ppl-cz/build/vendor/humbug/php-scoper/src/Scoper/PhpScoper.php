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

use PPLCZVendor\Humbug\PhpScoper\PhpParser\Printer\Printer;
use PPLCZVendor\Humbug\PhpScoper\PhpParser\TraverserFactory;
use PPLCZVendor\PhpParser\Error as PhpParserError;
use PPLCZVendor\PhpParser\Lexer;
use PPLCZVendor\PhpParser\Parser;
use function basename;
use function func_get_args;
use function ltrim;
use function preg_match as native_preg_match;
final class PhpScoper implements Scoper
{
    private const FILE_PATH_PATTERN = '/\\.php$/';
    private const NOT_FILE_BINARY = '/\\..+?$/';
    private const PHP_TAG = '/^<\\?php/';
    private const PHP_BINARY = '/^#!.+?php.*\\n{1,}<\\?php/';
    private Parser $parser;
    private Scoper $decoratedScoper;
    private TraverserFactory $traverserFactory;
    private Printer $printer;
    private Lexer $lexer;
    public function __construct(Parser $parser, Scoper $decoratedScoper, TraverserFactory $traverserFactory, Printer $printer, Lexer $lexer)
    {
        $this->parser = $parser;
        $this->decoratedScoper = $decoratedScoper;
        $this->traverserFactory = $traverserFactory;
        $this->printer = $printer;
        $this->lexer = $lexer;
    }
    /**
     * Scopes PHP files.
     *
     * @throws PhpParserError
     */
    public function scope(string $filePath, string $contents) : string
    {
        if (!self::isPhpFile($filePath, $contents)) {
            return $this->decoratedScoper->scope(...func_get_args());
        }
        return $this->scopePhp($contents);
    }
    public function scopePhp(string $php) : string
    {
        $statements = $this->parser->parse($php);
        $oldTokens = $this->lexer->getTokens();
        $scopedStatements = $this->traverserFactory->create($this)->traverse($statements);
        return $this->printer->print($scopedStatements, $scopedStatements, $oldTokens);
    }
    private static function isPhpFile(string $filePath, string $contents) : bool
    {
        if (1 === native_preg_match(self::FILE_PATH_PATTERN, $filePath)) {
            return \true;
        }
        if (1 === native_preg_match(self::NOT_FILE_BINARY, basename($filePath))) {
            return \false;
        }
        if (1 === native_preg_match(self::PHP_TAG, ltrim($contents))) {
            return \true;
        }
        return 1 === native_preg_match(self::PHP_BINARY, $contents);
    }
}
