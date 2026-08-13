<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeedbackToConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conditions', function (Blueprint $table) {
            $table->text('feedback')->nullable()->after('meals_memo');
            $table->unsignedBigInteger('feedback_by')->nullable()->after('feedback');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conditions', function (Blueprint $table) {
            $table->dropColumn([
                'feedback',
                'feedback_by'
            ]);
        });
    }
}
