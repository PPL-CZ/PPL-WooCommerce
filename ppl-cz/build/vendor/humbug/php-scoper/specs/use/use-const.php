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
    'title' => 'Use statements for constants',
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
], 'Constant use statement for a constant belonging to the global namespace' => <<<'PHP'
<?php

use const FOO;

----
<?php

namespace Humbug;

use const Humbug\FOO;

PHP
, 'Constant use statement for a constant belonging to the global namespace with global exposed enabled' => ['expose-global-constants' => \true, 'payload' => <<<'PHP'
<?php

use const FOO;

----
<?php

namespace Humbug;

use const FOO;

PHP
], 'Constant use statement for an internal constant belonging to the global namespace' => <<<'PHP'
<?php

use const DIRECTORY_SEPARATOR;

----
<?php

namespace Humbug;

use const DIRECTORY_SEPARATOR;

PHP
, 'Constant use statement for a constant belonging to the global namespace and which has already been prefixed' => <<<'PHP'
<?php

use const Humbug\FOO;

----
<?php

namespace Humbug;

use const Humbug\FOO;

PHP
, 'Constant use statement for a namespaced constant' => <<<'PHP'
<?php

use const Foo\BAR;

----
<?php

namespace Humbug;

use const Humbug\Foo\BAR;

PHP
, 'Constant use statement for a namespaced constant which has already been prefixed' => <<<'PHP'
<?php

use const Humbug\Foo\BAR;

----
<?php

namespace Humbug;

use const Humbug\Foo\BAR;

PHP
, 'Constant use statement for a namespaced constant which has been exposed' => ['expose-constants' => ['PPLCZVendor\\Foo\\BAR'], 'payload' => <<<'PHP'
<?php

use const Foo\BAR;

----
<?php

namespace Humbug;

use const Foo\BAR;

PHP
], 'Constant use statement for a namespaced constant which has NOT been exposed' => ['expose-constants' => ['/^Foo\\\\Baru.*$/'], 'payload' => <<<'PHP'
<?php

use const Foo\BAR;

----
<?php

namespace Humbug;

use const Humbug\Foo\BAR;

PHP
]];
