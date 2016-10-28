<?php
/**
 * Created by linzhv@outlook.com.
 * User: linzh
 * Date: 2016/7/1
 * Time: 11:53
 */
return [
    //当前激活的CDN方案
    'active_index'    => 0,
    //方案列表
    'solution_list' => [
        0   => [
            'style'   => [
                'assets/plugins/bootstrap/css/bootstrap.min.css',
                'assets/plugins/font-awesome/css/font-awesome.min.css',
                'assets/plugins/fontawesome4.2/css/font-awesome.min.css',
                'assets/plugins/toastr/toastr.min.css',

                'assets/css/themes/darkblue.css',
                'assets/css/dazzling.css',
            ],
            'script'    => [
                'assets/plugins/bootstrap/js/bootstrap.min.js',
                'assets/plugins/toastr/toastr.min.js',
                'assets/plugins/bootstrap-contextmenu/bootstrap-contextmenu.js',

                'assets/js/dazzling.js',
            ],
            //IE8(兼容)
            'compatible'    =>[
                'assets/plugins/html5shiv/dist/html5shiv.min.js',
                'assets/plugins/respond/dest/respond.min.js',
                'assets/plugins/jquery-placeholder/jquery.placeholder.min.js',
                'assets/js/jquery-1.11.3.min.js',
            ],
            //非IE或者大于等于9(性能)
            'performance'    => [
                'assets/js/jquery-2.2.3.min.js',
            ],
            'ico'   => [
                'https://elementary.io/favicon.ico',
            ]
        ],
    ]
];