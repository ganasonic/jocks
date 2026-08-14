<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHealthFieldsToConditionsTable extends Migration
{
    public function up()
    {
        Schema::table('conditions', function (Blueprint $table) {

            // 身体データ
            $table->decimal('body_weight', 5, 2)
                  ->nullable()
                  ->after('body_temperature');

            $table->unsignedSmallInteger('resting_heart_rate')
                  ->nullable()
                  ->after('body_weight');

            $table->unsignedSmallInteger('systolic_blood_pressure')
                  ->nullable()
                  ->after('resting_heart_rate');

            $table->unsignedSmallInteger('diastolic_blood_pressure')
                  ->nullable()
                  ->after('systolic_blood_pressure');

            $table->unsignedTinyInteger('spo2')
                  ->nullable()
                  ->after('diastolic_blood_pressure');

            // 女性コンディション
            $table->boolean('menstruation')
                  ->nullable()
                  ->after('spo2');

            $table->date('menstruation_start_date')
                  ->nullable()
                  ->after('menstruation');

            $table->unsignedTinyInteger('menstruation_condition')
                  ->nullable()
                  ->after('menstruation_start_date');

            $table->text('menstruation_memo')
                  ->nullable()
                  ->after('menstruation_condition');
        });
    }

    public function down()
    {
        Schema::table('conditions', function (Blueprint $table) {

            $table->dropColumn([
                'body_weight',
                'resting_heart_rate',
                'systolic_blood_pressure',
                'diastolic_blood_pressure',
                'spo2',
                'menstruation',
                'menstruation_start_date',
                'menstruation_condition',
                'menstruation_memo',
            ]);
        });
    }
}
