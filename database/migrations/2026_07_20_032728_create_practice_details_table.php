<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePracticeDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('practice_details', function (Blueprint $table) {
            $table->bigIncrements('id'); // ★ bigIncrements に修正
            $table->unsignedBigInteger('practice_id'); // ★ unsignedBigInteger に修正
            $table->string('menu_name');
            $table->string('runs_or_time')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();       // 選手自身の評価
            $table->string('video_url')->nullable();
            $table->text('impression')->nullable();
            $table->text('notice')->nullable();
            $table->unsignedTinyInteger('coach_rating')->nullable(); // コーチの評価
            $table->text('coach_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('practice_details');
    }
}
