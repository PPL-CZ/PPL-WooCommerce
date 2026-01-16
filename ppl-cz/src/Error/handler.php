<?php

function pplcz_format_args($args) {
    $formatted = [];
    foreach ($args as $arg) {
        if (is_object($arg)) {
            $formatted[] = get_class($arg);
        } elseif (is_array($arg)) {
            $formatted[] = 'Array(' . count($arg) . ')';
        } elseif (is_resource($arg)) {
            $formatted[] = 'Resource';
        } elseif (is_null($arg)) {
            $formatted[] = 'NULL';
        } elseif (is_bool($arg)) {
            $formatted[] = $arg ? 'true' : 'false';
        } elseif (is_string($arg)) {
            $formatted[] = "'" . (mb_strlen($arg) > 70 ? mb_substr($arg, 0, 70) . '...' : $arg) . "'";
        } else {
            $formatted[] = (string) $arg;
        }
    }
    return join(', ', $formatted);
}

function pplcz_add_log_to_options($addtotable, $hash)
{
    global $wpdb;
    if ($addtotable) {
        $prepare = $wpdb->prepare(
            "UPDATE {$wpdb->prefix}options
SET option_value = CAST((
    CASE
        WHEN option_value REGEXP '^[0-9]+$'
        THEN CAST(option_value AS UNSIGNED) + 1
        ELSE 1
    END
) AS CHAR)
WHERE option_name = %s", pplcz_create_name("error_log"));
        $wpdb->query($prepare);
    }

    $prepare = $wpdb->prepare(
        "UPDATE {$wpdb->prefix}options
SET option_value = trim(concat(ifnull(option_value, ''), '\n', %s))
WHERE option_name = %s and option_value not like %s", $hash, pplcz_create_name("error_log_hashes"), '%' . $hash .'%');

    $wpdb->query($prepare);

}


function pplcz_error_handler ($errno, $errstr, $errfile, $errline) {
    static $resolve;
    if ($resolve)
        return;
    $resolve = true;

    $backtrace = debug_backtrace();
    $path = realpath(__DIR__ . '/../..');
    $inplugin = strpos($errfile, $path) !== false;
    $out= [
        $errstr,
        "Stack trace:",

    ];

    foreach ($backtrace as $key => $frame)
    {
        $file = "emptyfile";
        if (isset($frame['file']))
            $file = $frame['file'];
        
        $inplugin = $inplugin || strpos($file, $path) !== false;
        $file = isset($frame['file']) ? $frame['file'] : '[internal function]';
        $line = isset($frame['line']) ? $frame['line'] : '';
        $function = isset($frame['function']) ? $frame['function'] : '';
        $args = isset($frame['args']) ? pplcz_format_args($frame['args']) : '';
        $out[] = "#$key $file($line): $function($args)";
    }

    if ($inplugin)
    {
        $max = get_option(pplcz_create_name("error_log"));
        $hashes = get_option(pplcz_create_name("error_log_hashes"));
        $error = $errstr . "\n" . join("\n", $out);
        $hash = sha1($error);
        if (intval($max) < 100 && strpos("$hashes" ?: '', $hash) === false) {
            global $wpdb;
            $show_errors = $wpdb->show_errors;
            $wpdb->hide_errors();
            try {
                $logdata = new \PPLCZ\Data\LogData();
                $logdata->set_message($error);
                $logdata->set_errorhash($hash);
                $logdata->set_timestamp(date('Y-m-d H:i:s'));
                $logdata->save();
                pplcz_add_log_to_options($logdata->get_id(), $logdata->get_errorhash());
            } catch (\Throwable $ex) {

            }
            $wpdb->show_errors = $show_errors;

        }
    }
    $resolve = false;
}


function pplcz_exception_handler($exception, $ignoreAdd = false)
{
    static $resolve;
    if ($resolve)
        return;
    $resolve = true;

    $path = realpath(__DIR__ . '/../..');
    $trace = $exception->getTraceAsString();
    $file = $exception->getFile();

    $inplugin = strpos($file, $path) !== false || strpos($trace, $path) !== false;

    if ($inplugin)
    {
        $error = get_class($exception) . ': ' . $exception->getMessage() . "\n";
        $error .= "File: " . $file . "(" . $exception->getLine() . ")\n";
        $error .= "Stack trace:\n" . $trace;

        $hash = sha1($error);
        $max = get_option(pplcz_create_name("error_log"));
        $hashes = get_option(pplcz_create_name("error_log_hashes"));

        if (($ignoreAdd || intval($max) < 100) && strpos("$hashes" ?: '', $hash) === false) {
            global $wpdb;
            $show_errors = $wpdb->show_errors;
            $wpdb->hide_errors();
            try {
                $logdata = new \PPLCZ\Data\LogData();
                $logdata->set_message($error);
                $logdata->set_errorhash($hash);
                $logdata->set_timestamp(date('Y-m-d H:i:s'));
                $logdata->save();
                pplcz_add_log_to_options($logdata->get_id(), $logdata->get_errorhash());
            } catch (\Throwable $ex) {
                // Tiché selhání
            }
            $wpdb->show_errors = $show_errors;
        }
    }

    $resolve = false;

    // Znovu vyhodit výjimku pro standardní zpracování PHP
    // throw $exception;
}

function pplcz_shutdown_handler()
{
    static $resolve;
    if ($resolve)
        return;
    $resolve = true;

    $error = error_get_last();
    if ($error)
    {
        $message = explode("\n", $error['message']);
        $path = realpath(__DIR__ . '/../../');
        if (array_filter($message, function($item) use($path){
            return strpos($item, $path) !== false;
        }))
        {
            $max = get_option(pplcz_create_name("error_log"));
            $hashes = get_option(pplcz_create_name("error_log_hashes"));
            $hash = sha1($error['message']);

            if (intval($max) < 100  && strpos("$hashes" ?: '', $hash) === false) {
                global $wpdb;
                $show_errors = $wpdb->show_errors;
                $wpdb->hide_errors();
                try {
                    $logdata = new \PPLCZ\Data\LogData();
                    $logdata->set_message($error['message']);
                    $logdata->set_errorhash(sha1($error['message']));
                    $logdata->set_timestamp(date('Y-m-d H:i:s'));
                    $logdata->save();
                    pplcz_add_log_to_options($logdata->get_id(), $logdata->get_errorhash());
                } catch (\Throwable $ex) {

                }
                $wpdb->show_errors = $show_errors;
            }
        }
    }

    $resolve = false;

}
set_error_handler("pplcz_error_handler");
register_shutdown_function("pplcz_shutdown_handler");