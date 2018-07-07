<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'cache' => [
        'adapter' => [
            'name'    => 'filesystem',
            'options' => [
                'ttl' => 120,
                'cacheDir' => 'data/cache',
            ],
        ],
        'plugins' => [
            // Don't throw exceptions on cache errors
            'exception_handler' => [
                'throw_exceptions' => false,
            ],
            // We store database rows on filesystem so we need to serialize them
            'Serializer',
        ],
    ],
];
