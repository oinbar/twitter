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
		Schema::connection('mysql')->create('feeds', function(Blueprint $table){			
			$table->integer('id')->unsigned();
			$table->string('criteria', 1000);			
			$table->string('update_rate');
			$table->timestamp('created_at');
			$table->primary(array('id', 'created_at'));
			$table->index('id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mysql')->drop('feeds');
	}

}
