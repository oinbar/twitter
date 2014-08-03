<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mysql')->create('users', function(Blueprint $table){
			$table->increments('id');
			$table->rememberToken();
			$table->string('username')->unique();						
			$table->string('password');
			$table->string('email')->unique();
			$table->string('privileges');
			$table->timestamps();
			$table->index('username');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mysql')->drop('users');
	}

}
