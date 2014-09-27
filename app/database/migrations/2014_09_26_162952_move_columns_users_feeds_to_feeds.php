<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveColumnsUsersFeedsToFeeds extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mysql')->table('users_feeds', function(Blueprint $table){			
			$table->dropColumn('feed_name');
			$table->dropColumn('feed_status');						
		});
		Schema::connection('mysql')->table('feeds', function(Blueprint $table){			
			$table->string('feed_name');
			$table->string('feed_status');						
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

	}
}
