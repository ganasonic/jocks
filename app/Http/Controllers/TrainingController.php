<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Training;
use App\TrainingDetail;

class TrainingController extends Controller
{
    public function create()
    {
        // 過去のユニークな種目名を一覧で取得（重複なし）
        $recentExercises = \App\TrainingDetail::orderBy('created_at', 'desc')
        ->limit(200)
        ->pluck('exercise_name')
        ->unique() // 重複を排除
        ->take(20) // 上位20件に絞る
        ->values(); // 配列のインデックスを詰める

    return view('trainings.create', compact('recentExercises'));
    }

    public function store(Request $request)
    {
        // 1. トレーニングのメインデータを保存
        $training = \App\Training::create([
            'user_id' => auth()->id(),
            'training_date' => $request->training_date,
        ]);

        // 2. 詳細（セット）データをループで保存
        if ($request->has('details')) {
            foreach ($request->details as $detail) {
                // 種目名が入力されている行だけ保存
                if (!empty($detail['exercise_name'])) {
                    $training->details()->create([
                        'exercise_name' => $detail['exercise_name'],
                        'weight'        => $detail['weight'] ?? null,
                        'reps'          => $detail['reps'] ?? null,
                        'sets'          => $detail['sets'] ?? null,
                        'interval'      => $detail['interval'] ?? null,
                        'rpe'           => $detail['rpe'] ?? null,
                    ]);
                }
            }
        }

        // ★ ここを /trainings から route('trainings.index') に修正します
        return redirect()->route('trainings.index')->with('success', 'トレーニングを記録しました！');
    }

    public function index()
    {
        // ログインユーザーのトレーニング記録を日付順（新しい順）で取得
        $trainings = \App\Training::where('user_id', auth()->id())
            ->orderBy('training_date', 'desc')
            ->with('details') // 詳細データも一緒に取得
            ->get();

        return view('trainings.index', compact('trainings'));
    }
}
