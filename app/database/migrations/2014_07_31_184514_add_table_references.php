<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableReferences extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mysql')->table('users_feeds', function(Blueprint $table){
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

		Schema::connection('mysql')->table('users_feeds', function(Blueprint $table){
			$table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mysql')->table('users_feeds', function($table){
			$table->dropForeign('users_feeds_user_id_foreign');
		});

		Schema::connection('mysql')->table('users_feeds', function($table){
			$table->dropForeign('users_feeds_feed_id_foreign');
		});
	}
}
