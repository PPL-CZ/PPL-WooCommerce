<?php

/*
 * This file is part of the Fidry\Console package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);
namespace PPLCZVendor\Fidry\Console\Application;

use PPLCZVendor\Fidry\Console\Input\IO;
interface ConfigurableIO
{
    /**
     * Configures the input and output instances based on the user arguments and
     * options. This is executed before running the application.
     */
    public function configureIO(IO $io) : void;
}
