<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModelsTransformations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
    {
        Schema::connection('mysql')->create('transformations_feeds', function (Blueprint $table) {
            $table->integer('feed_id')->unsigned();
            $table->string('transformation');
            $table->string('params')->nullable();
        });

        Schema::connection('mysql')->create('views_feeds', function (Blueprint $table) {
            $table->integer('feed_id')->unsigned();
            $table->string('view');
            $table->string('params')->nullable();
        });

        Schema::connection('mysql')->table('feeds', function (Blueprint $table) {
            $table->string('type');
            $table->renameColumn('criteria', 'params');
        });
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::connection('mysql')->drop('transformations_feeds');
        Schema::connection('mysql')->drop('views_feeds');
        Schema::connection('mysql')->table('feeds', function (Blueprint $table) {
            $table->renameColumn('params', 'criteria');
        });
	}
}
