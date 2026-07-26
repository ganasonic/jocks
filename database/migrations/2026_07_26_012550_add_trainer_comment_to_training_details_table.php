<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrainerCommentToTrainingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_details', function (Blueprint $table) {
            $table->text('feedback')
                ->nullable()
                ->after('rpe');
            //
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
            $table->dropColumn('feedback');
        });
    }
}
