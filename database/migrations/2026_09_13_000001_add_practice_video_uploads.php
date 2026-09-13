<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPracticeVideoUploads extends Migration
{
    public function up()
    {
        // Keep existing URL values intact. SQLite's strings have no length limit.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE practice_details MODIFY video_url TEXT NULL');
        } elseif (DB::getDriverName() !== 'sqlite') {
            throw new \RuntimeException('This migration supports MySQL and SQLite.');
        }

        Schema::create('practice_video_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('token')->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('player_id')->index();
            $table->unsignedBigInteger('detail_id')->nullable()->index();
            $table->string('original_name');
            $table->string('path')->unique();
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('total_size');
            $table->unsignedInteger('next_chunk')->default(0);
            $table->string('status')->default('pending')->index();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('practice_video_uploads');
        // Retain TEXT: shrinking to VARCHAR(255) would truncate multiple paths.
    }
}
