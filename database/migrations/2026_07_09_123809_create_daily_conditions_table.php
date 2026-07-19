<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->bigIncrements('id');

// 【修正後】単なる bigInteger のカラム定義のみにします（外部キー制約は設定しない）
            $table->unsignedBigInteger('user_id');

            $table->date('date'); // 記録対象日
            $table->decimal('body_temperature', 4, 2)->nullable(); // 体温
            $table->unsignedTinyInteger('condition_level'); // 体調 (1〜5)
            $table->unsignedTinyInteger('mood_level'); // 気分 (1〜5)
            $table->time('wakeup_time')->nullable(); // 起床時間
            $table->time('bedtime')->nullable(); // 就寝時間
            $table->text('meals_memo')->nullable(); // 食事メモ
            $table->timestamps();

            // 同じユーザーが同じ日に2回登録できないように複合ユニーク制約を設定
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conditions');
    }
}
