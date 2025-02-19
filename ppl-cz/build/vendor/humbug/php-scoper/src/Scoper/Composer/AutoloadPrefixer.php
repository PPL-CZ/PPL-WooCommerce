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
namespace PPLCZVendor\Humbug\PhpScoper\Scoper\Composer;

use PPLCZVendor\Humbug\PhpScoper\Symbol\EnrichedReflector;
use stdClass;
use function array_map;
use function array_merge;
use function is_array;
use function is_string;
use function rtrim;
use function PPLCZVendor\Safe\sprintf;
use function PPLCZVendor\Safe\substr;
use function str_replace;
use function strpos;
/**
 * @private
 */
final class AutoloadPrefixer
{
    private string $prefix;
    private EnrichedReflector $enrichedReflector;
    public function __construct(string $prefix, EnrichedReflector $enrichedReflector)
    {
        $this->prefix = $prefix;
        $this->enrichedReflector = $enrichedReflector;
    }
    /**
     * @param stdClass $contents Decoded JSON
     *
     * @return stdClass Prefixed decoded JSON
     */
    public function prefixPackageAutoloadStatements(stdClass $contents) : stdClass
    {
        if (isset($contents->autoload)) {
            $contents->autoload = self::prefixAutoloadStatements($contents->autoload, $this->prefix, $this->enrichedReflector);
        }
        if (isset($contents->{'autoload-dev'})) {
            $contents->{'autoload-dev'} = self::prefixAutoloadStatements($contents->{'autoload-dev'}, $this->prefix, $this->enrichedReflector);
        }
        if (isset($contents->extra->laravel->providers)) {
            $contents->extra->laravel->providers = self::prefixLaravelProviders($contents->extra->laravel->providers, $this->prefix, $this->enrichedReflector);
        }
        return $contents;
    }
    private static function prefixAutoloadStatements(stdClass $autoload, string $prefix, EnrichedReflector $enrichedReflector) : stdClass
    {
        if (!isset($autoload->{'psr-4'}) && !isset($autoload->{'psr-0'})) {
            return $autoload;
        }
        if (isset($autoload->{'psr-0'})) {
            [$psr4, $classMap] = self::transformPsr0ToPsr4AndClassmap((array) $autoload->{'psr-0'}, (array) ($autoload->{'psr-4'} ?? new stdClass()), (array) ($autoload->{'classmap'} ?? new stdClass()));
            if ([] === $psr4) {
                unset($autoload->{'psr-4'});
            } else {
                $autoload->{'psr-4'} = $psr4;
            }
            if ([] === $classMap) {
                unset($autoload->{'classmap'});
            } else {
                $autoload->{'classmap'} = $classMap;
            }
        }
        unset($autoload->{'psr-0'});
        if (isset($autoload->{'psr-4'})) {
            $autoload->{'psr-4'} = self::prefixAutoload((array) $autoload->{'psr-4'}, $prefix, $enrichedReflector);
        }
        return $autoload;
    }
    private static function prefixAutoload(array $autoload, string $prefix, EnrichedReflector $enrichedReflector) : array
    {
        $loader = [];
        foreach ($autoload as $namespace => $paths) {
            $newNamespace = $enrichedReflector->isExcludedNamespace($namespace) ? $namespace : sprintf('%s\\%s', $prefix, $namespace);
            $loader[$newNamespace] = $paths;
        }
        return $loader;
    }
    /**
     * @param array<string, (string|string[])> $psr0
     * @param (string|string[])[]              $psr4
     * @param string[]                         $classMap
     */
    private static function transformPsr0ToPsr4AndClassmap(array $psr0, array $psr4, array $classMap) : array
    {
        foreach ($psr0 as $namespace => $path) {
            //Append backslashes, if needed, since psr-0 does not require this
            if ('\\' !== substr($namespace, -1)) {
                $namespace .= '\\';
            }
            if (\false !== strpos($namespace, '_')) {
                $classMap[] = $path;
                continue;
            }
            $path = self::updatePSR0Path($path, $namespace);
            if (!isset($psr4[$namespace])) {
                $psr4[$namespace] = $path;
                continue;
            }
            $psr4[$namespace] = self::mergeNamespaces($namespace, $path, $psr4);
        }
        return [$psr4, $classMap];
    }
    /**
     * @param string|string[] $path
     *
     * @return string|string[]
     */
    private static function updatePSR0Path($path, string $namespace)
    {
        $namespaceForPsr = rtrim(str_replace('\\', '/', $namespace), '/');
        if (!is_array($path)) {
            if ('/' !== substr($path, -1)) {
                $path .= '/';
            }
            $path .= $namespaceForPsr . '/';
            return $path;
        }
        foreach ($path as $key => $item) {
            if ('/' !== substr($item, -1)) {
                $item .= '/';
            }
            $item .= $namespaceForPsr . '/';
            $path[$key] = $item;
        }
        return $path;
    }
    /**
     * Deals with the 4 possible scenarios:
     *       PSR0 | PSR4
     * array      |
     * string     |
     * or simply the namespace not existing as a psr-4 entry.
     *
     * @param string              $psr0Namespace
     * @param string|string[]     $psr0Path
     * @param (string|string[])[] $psr4
     *
     * @return string|string[]
     */
    private static function mergeNamespaces(string $psr0Namespace, $psr0Path, array $psr4)
    {
        // Both strings
        if (is_string($psr0Path) && is_string($psr4[$psr0Namespace])) {
            return [$psr4[$psr0Namespace], $psr0Path];
        }
        // PSR-4 is string, and PSR-0 is array
        if (is_array($psr0Path) && is_string($psr4[$psr0Namespace])) {
            $psr0Path[] = $psr4[$psr0Namespace];
            return $psr0Path;
        }
        // PSR-4 is array and PSR-0 is string
        if (is_string($psr0Path) && is_array($psr4[$psr0Namespace])) {
            $psr4[$psr0Namespace][] = $psr0Path;
            return $psr4[$psr0Namespace];
        }
        if (is_array($psr0Path) && is_array($psr4[$psr0Namespace])) {
            return array_merge($psr4[$psr0Namespace], $psr0Path);
        }
        return $psr0Path;
    }
    private static function prefixLaravelProviders(array $providers, string $prefix, EnrichedReflector $enrichedReflector) : array
    {
        return array_map(static fn(string $provider) => $enrichedReflector->isExcludedNamespace($provider) ? $provider : sprintf('%s\\%s', $prefix, $provider), $providers);
    }
}
