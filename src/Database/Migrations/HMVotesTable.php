<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HMVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        /*
         * Getting Tables Information from the Config File
         */
        $TableNames = config('HelperModels.Likes.Structure.TableNames');
        Schema::create($TableNames['Votes'], function (Blueprint $table){
            $table->increments('ID');
            $table->string('Voteable_type');
            $table->bigInteger('Voteable_id')->unsigned();
            $table->string('Voter_type');
            $table->bigInteger('Voter_id')->unsigned();
            $table->boolean('Vote')->default(config('ModelHelpers.Settings.Votes.DefaultSettings.Default'));
            $table->timestamp('CreatedAt')->nullable();
            $table->timestamp('UpdatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
         * Getting information from the config file
         */
        $TableNames = config('HelperModels.Likes.Structure.TableNames');
        Schema::dropIfExists($TableNames['Votes']);
    }
}