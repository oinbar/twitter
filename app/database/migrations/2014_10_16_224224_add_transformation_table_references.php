<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransformationTableReferences extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->table('transformations_feeds', function(Blueprint $table){
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade');
        });

        Schema::connection('mysql')->table('views_feeds', function(Blueprint $table){
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
        Schema::connection('mysql')->table('transformations_feeds', function($table){
            $table->dropForeign('transformations_feeds_feed_id_foreign');
        });

        Schema::connection('mysql')->table('views_feeds', function($table){
            $table->dropForeign('views_feeds_feed_id_foreign');
        });
    }
}
