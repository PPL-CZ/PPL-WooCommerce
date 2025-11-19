<?php
namespace PPLCZ\Languages;

class Load
{
    public static function load_script_translation_file($file, $handle, $domain)
    {
        $original = $file;
        if ($domain === 'ppl-cz') {
            $path = dirname(plugin_dir_path(__FILE__)) . '/Languages';
            $file = trim(str_replace($path, '', $file), '/\\');
            $file = str_replace('-'. $handle, '', $file);
            $file = $path . '/' . $file;

            if (file_exists($file))
                return $file;

        }
        return $original;
    }

    public static  function register()
    {
        $file = add_filter( 'load_script_translation_file', [self::class, "load_script_translation_file"], 10, 3 );
    }
}