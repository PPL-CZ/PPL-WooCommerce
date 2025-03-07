<?php

declare (strict_types=1);
namespace PPLCZVendor\Humbug\PhpScoper\Configuration;

use function array_pop;
use function count;
use function explode;
use function in_array;
use function preg_last_error;
use function preg_last_error_msg;
use function preg_match as native_preg_match;
use function PPLCZVendor\Safe\sprintf;
use function str_split;
use function strlen;
final class RegexChecker
{
    // https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
    private const PATTERN_MODIFIERS = ['i', 'm', 's', 'x', 'A', 'D', 'S', 'U', 'X', 'J', 'u'];
    public function isRegexLike(string $value) : bool
    {
        $valueLength = strlen($value);
        if ($valueLength < 2) {
            return \false;
        }
        $firstCharacter = $value[0];
        if (!self::isValidDelimiter($firstCharacter)) {
            return \false;
        }
        $parts = explode($firstCharacter, $value);
        if (\false === $parts || count($parts) !== 3) {
            return \false;
        }
        $lastPart = array_pop($parts);
        if (!self::isValidRegexFlags($lastPart)) {
            return \false;
        }
        return \true;
    }
    public function validateRegex(string $regex) : ?string
    {
        if (@native_preg_match($regex, '') !== \false) {
            return null;
        }
        return sprintf('Invalid regex: %s (code %s)', preg_last_error_msg(), preg_last_error());
    }
    private static function isValidDelimiter(string $delimiter) : bool
    {
        // This is not ideal as not true but is good enough for our case.
        // See https://github.com/humbug/php-scoper/issues/597
        return '\\' !== $delimiter && native_preg_match('/^\\p{L}$/u', $delimiter) === 0;
    }
    private static function isValidRegexFlags(string $value) : bool
    {
        if ('' === $value) {
            return \true;
        }
        $characters = str_split($value);
        foreach ($characters as $character) {
            if (!in_array($character, self::PATTERN_MODIFIERS, \true)) {
                return \false;
            }
        }
        return \true;
    }
}
