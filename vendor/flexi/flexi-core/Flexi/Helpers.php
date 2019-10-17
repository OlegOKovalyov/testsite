<?php

function dd()
{
    echo '<pre>';
    array_map(function ($x) {
        var_dump($x);
    }, func_get_args());
    die;
}

function getUserIp()
{
    $userIp = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $userIp = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $userIp;
}

function setDebugModesByIps($ips = [], $debug = 0, $debugDisplay = 0, $debugLog = 0)
{
    $userIp = getUserIp();
    if (!$debug || !in_array($userIp, $ips)) {
        return;
    }

    error_reporting(E_ALL);

    if ($debugDisplay) {
        ini_set('display_errors', 1);
    }

    if ($debugLog) {
        ini_set('log_errors', 1);
        ini_set('error_log', WP_CONTENT_DIR . '/debug.log');
    }
}

function getThemeDirectory($path = '')
{
    return get_template_directory() . $path;
}

function getThemeUrl($path = '')
{
    return get_template_directory_uri() . $path;
}

function strReplaceOnce($search, $replace, $text)
{
    $pos = strpos($text, $search);
    return $pos !== false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
}

function getCoreConstant($name)
{
    $constant = constant("Flexi\Constants::$name");
    return apply_filters('get_core_constant', $constant, $name);
}
