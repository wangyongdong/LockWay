<?php

function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $prefix = 'src\Lock\\';
    $path = str_replace($prefix, '', $path);
    $file = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $path . '.php';
    if (file_exists($file)) {
        echo $file.'<br/>';
        require_once $file;
    }
}

spl_autoload_register('classLoader', true);
