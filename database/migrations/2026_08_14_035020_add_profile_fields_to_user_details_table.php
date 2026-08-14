<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {

            $table->string('team_name', 100)
                  ->nullable()
                  ->after('affiliation');

            $table->string('gender', 20)
                  ->nullable()
                  ->after('team_name');

            $table->string('phone', 30)
                  ->nullable()
                  ->after('gender');

            $table->string('line_id', 100)
                  ->nullable()
                  ->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn([
                'team_name',
                'gender',
                'phone',
                'line_id',
            ]);
        });
    }
}
