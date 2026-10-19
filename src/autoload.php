<?php
/**
 * CardVault Universal Autoloader
 * First checks for Composer's vendor/autoload.php, then falls back to PSR-4 SPL autoloader.
 */

// Ensure unified timezone across environments
date_default_timezone_set('Asia/Bangkok');

$composerAutoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'CardVault\\';
        $baseDir = __DIR__ . '/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    });
}
