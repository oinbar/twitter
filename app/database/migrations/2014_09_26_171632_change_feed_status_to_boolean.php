<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFeedStatusToBoolean extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mysql')->table('feeds', function(Blueprint $table){			
			$table->dropColumn('feed_status');						
		});
		Schema::connection('mysql')->table('feeds', function(Blueprint $table){			
			$table->boolean('feed_status');						
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
