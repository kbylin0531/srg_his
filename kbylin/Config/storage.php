<<<<<<< HEAD
<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/16
 * Time: 16:22
 */
return [
    'DRIVER_DEFAULT_INDEX' => 0,
    'DRIVER_CLASS_LIST' => [
        \System\Core\Storage\File::class,
    ],
    'DRIVER_CONFIG_LIST' => [
        [
            'READ_LIMIT_ON'     => true,
            'WRITE_LIMIT_ON'    => true,
            'READABLE_SCOPE'    => BASE_PATH,
            'WRITABLE_SCOPE'    => RUNTIME_PATH,
            'ACCESS_FAILED_MODE'    => MODE_RETURN,
        ]
    ],

    //方面考虑
    'READ_LIMIT_ON'     => true,
    'WRITE_LIMIT_ON'    => true,
    'READABLE_SCOPE'    => BASE_PATH,
    'WRITABLE_SCOPE'    => RUNTIME_PATH,
=======
<?php
/**
 * Created by PhpStorm.
 * User: linzh_000
 * Date: 2016/3/16
 * Time: 16:22
 */
return [
    'DRIVER_DEFAULT_INDEX' => 0,
    'DRIVER_CLASS_LIST' => [
        \System\Core\Storage\File::class,
    ],
    'DRIVER_CONFIG_LIST' => [
        [
            'READ_LIMIT_ON'     => true,
            'WRITE_LIMIT_ON'    => true,
            'READABLE_SCOPE'    => BASE_PATH,
            'WRITABLE_SCOPE'    => RUNTIME_PATH,
            'ACCESS_FAILED_MODE'    => MODE_RETURN,
        ]
    ],

    //方面考虑
    'READ_LIMIT_ON'     => true,
    'WRITE_LIMIT_ON'    => true,
    'READABLE_SCOPE'    => BASE_PATH,
    'WRITABLE_SCOPE'    => RUNTIME_PATH,
>>>>>>> 5074fdd666065b44da9222b2537f8dec20deeb5f
];