<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_staff', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('player_id')
                  ->comment('選手ID');

            $table->unsignedBigInteger('staff_id')
                  ->comment('スタッフID');

            $table->timestamps();

            // 同じ担当は登録不可
            $table->unique(['player_id', 'staff_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_staff');
    }
}
