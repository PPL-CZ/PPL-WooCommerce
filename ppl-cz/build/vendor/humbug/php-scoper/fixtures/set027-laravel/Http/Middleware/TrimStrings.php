<?php

namespace PPLCZVendor\App\Http\Middleware;

use PPLCZVendor\Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;
class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = ['password', 'password_confirmation'];
}
