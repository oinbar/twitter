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

		Schema::connection('mysql')->table('feeds', function(Blueprint $table){
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
		Schema::connection('mysql')->table('users_feeds', function(Blueprint $table){
			$table->dropForeign('users_feeds_user_id_foreign');
		});

		Schema::connection('mysql')->table('feeds', function(Blueprint $table){
			$table->dropForeign('feeds_id_foreign');
		});
	}
}
