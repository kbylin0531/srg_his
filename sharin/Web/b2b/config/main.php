<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

include __DIR__.'/b2bcate.php';

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.components.B2BCategory.*',//b2b行业分类获取
        'application.components.B2BMember.*',
        'application.components.B2BProduct.*',
        'application.components.InformationProvider.*',
    ),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
                BASE_URL.'/<controller:\w+>/<id:\d+>'=>'<controller>/view',
                BASE_URL.'/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                BASE_URL.'/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'sqlite'=>array(
			'connectionString' => 'sqlite:'.PATH_DATA.'pub.db',
		),
		// uncomment the following to use a MySQL database
		'db'=>array(
            'class'=>'system.db.CDbConnection',
            'connectionString' => 'mysql:host=192.168.99.99;dbname=bossgoo',
            'username' => 'bossgoo',
            'password' => 'bossgoo',
            'charset' => 'utf8',
            'tablePrefix' => 'nt_',
            'emulatePrepare' => true,
            'enableProfiling' => true,
            'schemaCachingDuration' => 3600,
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);