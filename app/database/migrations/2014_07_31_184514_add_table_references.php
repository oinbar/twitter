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
		Schema::table('users_feeds', function($table){
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

		Schema::table('feeds', function($table){
			$table->foreign('id')->references('feed_id')->on('users_feeds')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users_feeds', function($table){
			$table->dropForeign('users_feeds_user_id_foreign');
		});

		Schema::table('feeds', function($table){
			$table->dropForeign('feeds_id_foreign');
		});
	}
}
