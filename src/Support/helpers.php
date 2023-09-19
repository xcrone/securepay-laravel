<?php

if (! function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string  $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (! function_exists('generate_checksum')) {
    /**
     * Generate checksum from given array and token.
     *
     * @param  array  $array
     * @param  string  $token
     * @return string
     */
    function generate_checksum($array, $token)
    {
        ksort($array);
        $string = implode('|', $array);
        return hash_hmac('sha256', $string, $token);
    }
}
