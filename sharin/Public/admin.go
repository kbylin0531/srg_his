<?php

const SR_DEBUG_MODE_ON = true;
const SR_PAGE_TRACE_ON = true;

include '../Sharin/web.module';

Sharin::init([
    'APP_NAME'              => 'Admin',//一个入口文件对应一个应用
    'SESSION_MEMCACHE_ON'   => true,
]);
Sharin::start();