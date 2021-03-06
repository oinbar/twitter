<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersFeedsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mysql')->create('users_feeds', function(Blueprint $table){
			$table->increments('feed_id');
			$table->integer('user_id')->unsigned();			
			$table->string('feed_name');
			$table->string('feed_status');
			$table->index('feed_id');
			$table->index('user_id');	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mysql')->drop('users_feeds');
	}

}
