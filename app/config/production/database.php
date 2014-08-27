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
		
		// OPENSHIFT		
		// 'mongodb' => array(
		//     'driver'   => 'mongodb',
		//     'host'     => '127.3.231.3',
		//     'port'     => '27017',
		//     'username' => 'admin',
		//     'password' => 'irDcWgiibvx7',
		//     'database' => 'twitterintel',
		// ),

		// AWS
		'mongodb' => array(
			// AWS
		    'driver'   => 'mongodb',
		    'host'     => 'ec2-54-87-178-57.compute-1.amazonaws.com',
		    'port'     => '27017',
		    'username' => 'admin',
		    'password' => 'password',
		    'database' => 'twitterintel',
		),

		// OPENSHIFT
		// 'mysql' => array(
		// 	'driver'    => 'mysql',
		// 	'host'      => '127.3.231.2',
		// 	'port'		=> '3306',
		// 	'database'  => 'twitterintel',
		// 	'username'  => 'admin4nSCiYX',
		// 	'password'  => '4NeDJtqkssj3',
		// ),

		// AWS
		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'twitterintel-mysql.c0sdmckg4fw5.us-east-1.rds.amazonaws.com',
			'port'		=> '3306',
			'database'  => 'twitterintel',
			'username'  => 'admin',
			'password'  => 'password',		
		),
		
	),

);
