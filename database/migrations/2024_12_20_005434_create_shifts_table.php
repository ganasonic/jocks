<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->date('shift_date'); // 日付
            $table->unsignedInteger('user_id'); // USERID
            // 外部キー制約を追加
            //$table->foreign('user_id')
            //    ->references('id')
            //    ->on('users')
            //    ->onDelete('cascade'); // 紐づけられたfamilyが削除されたら、このuser_idも削除
            $table->boolean('breakfast'); // 朝食
            $table->boolean('am_shift'); // 午前
            $table->boolean('lunch'); // 昼食
            $table->boolean('pm_shift'); // 午後
            $table->boolean('dinner'); // 夕食
            $table->boolean('stay'); // 宿泊
            $table->boolean('bus_outward'); // バス往路
            $table->boolean('bus_return'); // バス復路
            $table->unsignedInteger('property')->nullable(); // 属性
            $table->string('comment')->nullable(); // コメント
            $table->boolean('flag')->nullable(); // 削除フラグ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shifts');
    }
}
