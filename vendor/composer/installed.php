<?php return array(
    'root' => array(
        'name' => '__root__',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => 'b0adafd15db2921970ff86634cebde9355ed0b90',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        '__root__' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'b0adafd15db2921970ff86634cebde9355ed0b90',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'phpfastcache/phpfastcache' => array(
            'pretty_version' => '9.2.3',
            'version' => '9.2.3.0',
            'reference' => '372d62e0bbab5bcfac6404dc912040b038a3afb7',
            'type' => 'library',
            'install_path' => __DIR__ . '/../phpfastcache/phpfastcache',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'psr/cache' => array(
            'pretty_version' => '3.0.0',
            'version' => '3.0.0.0',
            'reference' => 'aa5030cfa5405eccfdcb1083ce040c2cb8d253bf',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/cache',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'psr/cache-implementation' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '2.0|3.0',
            ),
        ),
        'psr/simple-cache' => array(
            'pretty_version' => '3.0.0',
            'version' => '3.0.0.0',
            'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/simple-cache',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'psr/simple-cache-implementation' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '2.0|3.0',
            ),
        ),
    ),
);
