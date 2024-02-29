<?php
require_once '/path/to/phpfastcache/autoload.php'; // Adjust the path to autoload.php as needed

use Phpfastcache\CacheManager;
use Phpfastcache\Drivers\File\Config as FileConfig;

$cacheConfig = new FileConfig([
    'path' => realpath(__DIR__) . '/cache', // The folder where caching will be created
    'securityKey' => 'my-random-security-key', // Can be the name of your project, used to create a folder inside the caching path
    'preventCacheSlams' => true,
    'cacheSlamsTimeout' => 20,
    'secureFileManipulation' => true
]);

CacheManager::setDefaultConfig($cacheConfig);
$cache = CacheManager::getInstance('Files');
