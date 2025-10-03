<?php
namespace PPLCZ\Admin;
defined("WPINC") or die();


class Errors extends \WP_Error
{
    public function add($code, $message, $data = '')
    {
        parent::add(ltrim($code, "."), $message, $data);
    }
}