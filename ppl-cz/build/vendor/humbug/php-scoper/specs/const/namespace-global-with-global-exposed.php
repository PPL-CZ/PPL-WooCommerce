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
    'title' => 'Global constant usage in a namespace with the global constants exposed',
    // Default values. If not specified will be the one used
    'prefix' => 'Humbug',
    'expose-global-constants' => \true,
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
], 'Constant call in a namespace' => <<<'PHP'
<?php

namespace A;

DUMMY_CONST;
----
<?php

namespace Humbug\A;

DUMMY_CONST;

PHP
, 'Exposed constant call in a namespace' => ['expose-constants' => ['DUMMY_CONST'], 'payload' => <<<'PHP'
<?php

namespace A;

DUMMY_CONST;
----
<?php

namespace Humbug\A;

DUMMY_CONST;

PHP
], 'FQ constant call in a namespace' => <<<'PHP'
<?php

namespace A;

\DUMMY_CONST;
----
<?php

namespace Humbug\A;

\DUMMY_CONST;

PHP
, 'Exposed FQ constant call in a namespace' => ['expose-constants' => ['DUMMY_CONST'], 'payload' => <<<'PHP'
<?php

namespace A;

\DUMMY_CONST;
----
<?php

namespace Humbug\A;

\DUMMY_CONST;

PHP
]];
