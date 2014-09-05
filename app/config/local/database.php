<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/
	'default' => 'mysql',

	'connections' => array(

		'mongodb' => array(
			// AWS
		    'driver'   => 'mongodb',
		    'host'     => 'ec2-54-87-178-57.compute-1.amazonaws.com',
		    'port'     => '27017',
		    'username' => 'admin',
		    'password' => 'password',
		    'database' => 'twitterintel',
		),
		// 'mongodb' => array(
		//     'driver'   => 'mongodb',
		//     'host'     => 'localhost',
		//     'port'     => 27017,
		//     'username' => 'username',
		//     'password' => 'password',
		//     'database' => 'twitterintelDB',
		// ),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'twittermanagerDB',
			'username'  => 'root',
			'password'  => 'root',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),

	),

);
