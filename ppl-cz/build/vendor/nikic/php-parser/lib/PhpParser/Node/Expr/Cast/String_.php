<?php

declare (strict_types=1);
namespace PPLCZVendor\PhpParser\Node\Expr\Cast;

use PPLCZVendor\PhpParser\Node\Expr\Cast;
class String_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_String';
    }
}
