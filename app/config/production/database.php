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
	'default' => 'mongodb',

	'connections' => array(

		'mongodb' => array(
		    'driver'   => 'mongodb',
		    'host'     => 'mongodb://admin:FdCPdQ4jTl3j@127.10.55.2',
		    'port'     => 27017,
		    'username' => 'admin',
		    'password' => 'FdCPdQ4jTl3j',
		    'database' => 'twitter',
		),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'mysql://admin1a7i5Kc:Jicpux7H78w2@127.10.55.3:3306/',
			'database'  => 'twitter',
			'username'  => 'admin1a7i5Kc',
			'password'  => 'Jicpux7H78w2',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),
	),

);
