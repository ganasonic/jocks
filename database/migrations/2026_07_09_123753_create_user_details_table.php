<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            // 外部キーの「連動設定」は外し、インデックス付きのただの数値カラムとして定義する
            $table->unsignedBigInteger('user_id')->unique();
            // ↓ この1行を削除（またはコメントアウト）します
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->date('birthdate')->nullable();     // 生年月日
            $table->string('affiliation')->nullable(); // 所属チーム・学校など

            // その他、選手に必要な固定属性を定義
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
        Schema::dropIfExists('user_details');
    }
}
