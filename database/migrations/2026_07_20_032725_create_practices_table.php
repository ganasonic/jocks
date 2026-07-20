<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePracticesTable extends Migration
{
    public function up()
    {
        Schema::create('practices', function (Blueprint $table) {
            $table->bigIncrements('id'); // ★ id() ではなく bigIncrements('id') に修正
            $table->unsignedBigInteger('user_id'); // ★ foreignId の代わりの記法
            $table->date('practice_date');
            $table->string('title')->nullable();
            $table->text('target')->nullable();
            $table->string('practice_type')->nullable();
            $table->text('overall_coach_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('practices');
    }
}
