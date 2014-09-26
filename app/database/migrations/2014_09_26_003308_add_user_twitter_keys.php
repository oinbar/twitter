<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTwitterKeys extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mysql')->table('users', function(Blueprint $table){			
			$table->string('twitter_oauth_access_token');
			$table->string('twitter_oauth_access_token_secret');			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mysql')->table('users', function(Blueprint $table){			
			$table->dropColumn('twitter_oauth_access_token');
			$table->dropColumn('twitter_oauth_access_token_secret');			
		});
	}
}
