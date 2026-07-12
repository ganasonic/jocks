<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->bigIncrements('id');
            // 誰の目標か（外部キー）
            $table->unsignedBigInteger('user_id');

            // 期間のタイプ（日、週、月、年など。文字数制限を少し長めに設定）
            $table->string('period_type', 30);

            // 期間の開始日と終了日（これで数日単位や複数月など柔軟に対応できます）
            $table->date('start_date');
            $table->date('end_date');

            // 【Plan: 計画】
            $table->string('title', 255);             // 目標（タイトル）
            $table->text('action_plan')->nullable();    // 行動プラン（具体的なアクション）

            // 【Do & Check: 実行と評価】
            $table->text('result_memo')->nullable();    // 結果
            $table->text('impression')->nullable();     // 感想
            $table->unsignedTinyInteger('achievement_rate')->nullable(); // 達成度 (0〜100%)

            // 【Action: 改善】
            $table->text('countermeasure')->nullable(); // 原因と対策
            $table->text('next_action')->nullable();    // 次への引き継ぎ事項

            // 【Coach: コーチ】
            $table->text('coach_comment')->nullable();  // コーチのコメント
            $table->unsignedBigInteger('coach_id')->nullable(); // コメントしたコーチのID（将来用）

            $table->timestamps();

            // 外部キー制約
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goals');
    }
}
