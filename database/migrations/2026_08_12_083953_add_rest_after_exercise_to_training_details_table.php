<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRestAfterExerciseToTrainingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_details', function (Blueprint $table) {
            $table->integer('rest_after_exercise')
                  ->nullable()
                  ->after('rpe');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_details', function (Blueprint $table) {
            $table->dropColumn('rest_after_exercise');
        });
    }
}
