<?php

declare (strict_types=1);
namespace PPLCZVendor;

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
return ['meta' => [
    'title' => 'Scalar literal assigned as key or value in an array',
    // Default values. If not specified will be the one used
    'prefix' => 'Humbug',
    'expose-global-constants' => \false,
    'expose-global-classes' => \false,
    'expose-global-functions' => \false,
    'expose-namespaces' => [],
    'expose-constants' => [],
    'expose-classes' => [],
    'expose-functions' => [],
    'exclude-namespaces' => [],
    'exclude-constants' => [],
    'exclude-classes' => [],
    'exclude-functions' => [],
    'expected-recorded-classes' => [],
    'expected-recorded-functions' => [],
], 'String argument' => <<<'PHP'
<?php

$x = [
    'Symfony\\Component\\Yaml\\Ya_1' => 'Symfony\\Component\\Yaml\\Ya_1',
    '\\Symfony\\Component\\Yaml\\Ya_1' => '\\Symfony\\Component\\Yaml\\Ya_1',
    'Humbug\\Symfony\\Component\\Yaml\\Ya_1' => 'Humbug\\Symfony\\Component\\Yaml\\Ya_1',
    '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1' => '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1',
    'Closure',
    'usedAttributes',
    'FOO',
    'PHP_EOL',
];

(new X)->foo()([
    'Symfony\\Component\\Yaml\\Ya_1' => 'Symfony\\Component\\Yaml\\Ya_1',
    '\\Symfony\\Component\\Yaml\\Ya_1' => '\\Symfony\\Component\\Yaml\\Ya_1',
    'Humbug\\Symfony\\Component\\Yaml\\Ya_1' => 'Humbug\\Symfony\\Component\\Yaml\\Ya_1',
    '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1' => '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1',
    'Closure',
    'usedAttributes',
    'FOO',
    'PHP_EOL',
]);

----
<?php

namespace Humbug;

$x = ['Humbug\\Symfony\\Component\\Yaml\\Ya_1' => 'Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Humbug\\Symfony\\Component\\Yaml\\Ya_1' => 'Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Humbug\\Symfony\\Component\\Yaml\\Ya_1' => 'Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Humbug\\Symfony\\Component\\Yaml\\Ya_1' => 'Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Closure', 'usedAttributes', 'FOO', 'PHP_EOL'];
(new X())->foo()(['Symfony\\Component\\Yaml\\Ya_1' => 'Symfony\\Component\\Yaml\\Ya_1', '\\Symfony\\Component\\Yaml\\Ya_1' => '\\Symfony\\Component\\Yaml\\Ya_1', 'Humbug\\Symfony\\Component\\Yaml\\Ya_1' => 'Humbug\\Symfony\\Component\\Yaml\\Ya_1', '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1' => '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Closure', 'usedAttributes', 'FOO', 'PHP_EOL']);

PHP
, 'Array item of a list' => <<<'PHP'
<?php

$array = ['locality' => 'Tunis', 'postal_code' => '1110'];
list('postal_code' => $zipCode, 'locality' => $locality) = $array;

----
<?php

namespace Humbug;

$array = ['locality' => 'Tunis', 'postal_code' => '1110'];
list('postal_code' => $zipCode, 'locality' => $locality) = $array;

PHP
, 'Array item of a list with class-like symbols' => <<<'PHP'
<?php

$array = ['Acme\locality' => 'Acme\Foo', 'Acme\postal_code' => 'Acme\Bar'];
list('Acme\postal_code' => $zipCode, 'Acme\locality' => $locality) = $array;

----
<?php

namespace Humbug;

$array = ['Humbug\\Acme\\locality' => 'Humbug\\Acme\\Foo', 'Humbug\\Acme\\postal_code' => 'Humbug\\Acme\\Bar'];
list('Acme\\postal_code' => $zipCode, 'Acme\\locality' => $locality) = $array;

PHP
];
