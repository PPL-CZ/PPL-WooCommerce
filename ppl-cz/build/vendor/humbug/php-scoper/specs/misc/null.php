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
    'title' => 'Null case-sensitivity',
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
], 'Usages of null' => <<<'PHP'
<?php

const LOWERCASE_NULL = null;
const UPPERCASE_NULL = null;

$lowerCaseNull = null;
$upperCaseNull = null;

function foo($lowerCaseNull = null, $upperCaseNull = NULL) {}

class X {
    var $lowerCaseNull = null;
    var $upperCaseNull = NULL;
}

----
<?php

namespace Humbug;

const LOWERCASE_NULL = null;
const UPPERCASE_NULL = null;
$lowerCaseNull = null;
$upperCaseNull = null;
function foo($lowerCaseNull = null, $upperCaseNull = NULL)
{
}
class X
{
    var $lowerCaseNull = null;
    var $upperCaseNull = NULL;
}

PHP
];
