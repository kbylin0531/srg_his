<?php
/**
 * Created by linzhv@outlook.com
 * User: asus
 * Date: 16-6-14
 * Time: 17:34
 */
//---------------------------------- environment constant -------------------------------------//
define('IS_CLIENT',PHP_SAPI === 'cli');
define('IS_WINDOWS',false !== stripos(PHP_OS, 'WIN'));
define('IS_REQUEST_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ));
define('IS_METHOD_POST',strtoupper($_SERVER['REQUEST_METHOD']) === 'POST');
define('REQUEST_TIME',$_SERVER['REQUEST_TIME']);

define ( 'HTTP_PREFIX', isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on' ? 'https://' : 'http://' );
//---------------------------------- mode constant -------------------------------------//
defined('DEBUG_MODE_ON') or define('DEBUG_MODE_ON', true);
defined('PAGE_TRACE_ON') or define('PAGE_TRACE_ON', true);//在处理微信签名检查时会发生以外的错误
//---------------------------------- variable type constant ------------------------------//
const TYPE_BOOL     = 'boolean';
const TYPE_INT      = 'integer';
const TYPE_FLOAT    = 'double';//double ,  float
const TYPE_STR      = 'string';
const TYPE_ARRAY    = 'array';
const TYPE_OBJ      = 'object';
const TYPE_RESOURCE = 'resource';
const TYPE_NULL     = 'NULL';
const TYPE_UNKNOWN  = 'unknown type';

//---------------------------------- path constant -------------------------------------//
$_dir1234567689     = dirname(dirname(__DIR__)).'/';
define('PATH_BASE', IS_WINDOWS?str_replace('\\','/',$_dir1234567689):$_dir1234567689);
const PATH_FRAMEWORK= PATH_BASE.'Soya/';
const PATH_CONFIG   = PATH_FRAMEWORK.'Config/';
const PATH_RUNTIME  = PATH_BASE.'Runtime/';
const PATH_PUBLIC  = PATH_BASE.'Public/';

const SINGLE_INSTANCE = false;
const JUST_INIT = false;
const DRIVER_INSTANCE = true;
