<?php
/**
 * The manifest of files that are local to specific environment.
 * This file returns a list of environments that the application
 * may be installed under. The returned data must be in the following
 * format:
 *
 * ```php
 * return [
 *     'environment name' => [
 *         'path' => 'directory storing the local files',
 *         'skipFiles'  => [
 *             // list of files that should only copied once and skipped if they already exist
 *         ],
 *         'setWritable' => [
 *             // list of directories that should be set writable
 *         ],
 *         'setExecutable' => [
 *             // list of files that should be set executable
 *         ],
 *         'setCookieValidationKey' => [
 *             // list of config files that need to be inserted with automatically generated cookie validation keys
 *         ],
 *         'createSymlink' => [
 *             // list of symlinks to be created. Keys are symlinks, and values are the targets.
 *         ],
 *     ],
 * ];
 * ```
 */
return [
    'Development' => [
        'path' => 'dev',
        'setWritable' => [
            'console/runtime',
            'backend/runtime',
            'backend/web/assets',
            'backendQltt/runtime',
            'backendQltt/web/assets',
            'frontend/runtime',
            'frontend/web/assets',
            'frontend/web/uploads',
            'frontend/web/uploads/identified',
            'resident/runtime',
            'resident/web/assets',
            'pay/runtime',
            'pay/web/assets',
        ],
        'setExecutable' => [
            'yii',
            'yii_test',
        ],
        'setCookieValidationKey' => [
            'common/config/codeception-local.php',
            'backend/config/main-local.php',
            'backendQltt/config/main-local.php',
            'frontend/config/main-local.php',
            'resident/config/main-local.php',
            'pay/config/main-local.php',
        ],
        'createSymlink' => [
             // list of symlinks to be created. Keys are symlinks, and values are the targets.
            'backend/web/uploads'  => 'frontend/web/uploads',
            'backendQltt/web/uploads'  => 'frontend/web/uploads',
            'resident/web/uploads'  => 'frontend/web/uploads',
        ],
    ],
    'Production' => [
        'path' => 'prod',
        'setWritable' => [
            'console/runtime',
            'backend/runtime',
            'backend/web/assets',
            'backendQltt/runtime',
            'backendQltt/web/assets',
            'frontend/runtime',
            'frontend/web/assets',
            'frontend/web/uploads',
            'frontend/web/uploads/identified',
            'resident/runtime',
            'resident/web/assets',
            'pay/runtime',
            'pay/web/assets',
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'backend/config/main-local.php',
            'backendQltt/config/main-local.php',
            'frontend/config/main-local.php',
            'resident/config/main-local.php',
            'pay/config/main-local.php',
        ],
        'createSymlink' => [
            // list of symlinks to be created. Keys are symlinks, and values are the targets.
            'backend/web/uploads'  => 'frontend/web/uploads',
            'backendQltt/web/uploads'  => 'frontend/web/uploads',
            'resident/web/uploads'  => 'frontend/web/uploads',
        ],
    ],
];
