<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNutritionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutrition_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nutrition_id');
            $table->string('meal_type');
            $table->string('photo_path')->nullable();
            $table->integer('calories')->nullable();
            $table->integer('protein')->nullable();
            $table->text('meal_memo')->nullable();
            $table->text('meal_coach_comment')->nullable();
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
        Schema::dropIfExists('nutrition_details');
    }
}
