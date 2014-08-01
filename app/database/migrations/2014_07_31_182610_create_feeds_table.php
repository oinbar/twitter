<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feeds', function($table){			
			$table->integer('feed_id')->unsigned();
			$table->string('criteria');
			$table->integer('version');
			$table->timestamp('created_at');
			$table->primary(array('feed_id', 'version'));
			$table->index('feed_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
