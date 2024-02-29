<?php
require_once 'vendor/autoload.php'; // Adjust the path to autoload.php as needed

/* Cache adapter for phpFastCache */
$cache_config = new \Phpfastcache\Drivers\Files\Config([
    'path' => realpath(__DIR__) . '/cache', // The folder where the caching will be created
    'securityKey' => 'my-random-security-key', // Can be the name of your project, will be used to create the folder inside the caching path
    'preventCacheSlams' => true,
    'cacheSlamsTimeout' => 20,
    'secureFileManipulation' => true
]);
\Phpfastcache\CacheManager::setDefaultConfig($cache_config);
$cache = \Phpfastcache\CacheManager::getInstance('Files');
