<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Training;
use App\TrainingDetail;

class TrainingController extends Controller
{
    // ログインしていないユーザーを自動的にログイン画面へリダイレクトする
    public function __construct()
    {
        $this->middleware('auth');
    }

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
        $userId = auth()->id();

        // 1. トレーニングのメインデータを保存
        $training = \App\Training::create([
            'user_id'       => $userId,
            'title'         => $request->title ?? 'ワークアウト', // ワークアウト名（空ならデフォルト値）
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
                        'rest_after_exercise' => $detail['rest_after_exercise'] ?? null,
                    ]);
                }
            }
        }

        // ★ ここを /trainings から route('trainings.index') に修正します
        return redirect()->route('trainings.index')->with('success', 'トレーニングを記録しました！');
    }

    // 編集画面表示
    public function edit(\App\Training $training)
    {
        // 他人のデータはいじれないようにチェック
        if ($training->user_id !== auth()->id()) {
            abort(403);
        }

        $recentExercises = \App\TrainingDetail::orderBy('created_at', 'desc')
            ->limit(200)
            ->pluck('exercise_name')
            ->unique()
            ->take(20)
            ->values();

        return view('trainings.edit', compact('training', 'recentExercises'));
    }

    // 更新処理
    public function update(Request $request, \App\Training $training)
    {
        if ($training->user_id !== auth()->id()) {
            abort(403);
        }

        // 1. メインデータの更新
        $training->update([
            'title'         => $request->title ?? 'ワークアウト',
            'training_date' => $request->training_date,
        ]);

        // 2. 既存の明細を一気に削除して作り直す（一番安全で確実な手法）
        $training->details()->delete();

        if ($request->has('details')) {
            foreach ($request->details as $detail) {
                if (!empty($detail['exercise_name'])) {
                    $training->details()->create([
                        'exercise_name'        => $detail['exercise_name'],
                        'weight'               => $detail['weight'] ?? null,
                        'reps'                 => $detail['reps'] ?? null,
                        'sets'                 => $detail['sets'] ?? null,
                        'interval'             => $detail['interval'] ?? null,
                        'rpe'                  => $detail['rpe'] ?? null,
                        'rest_after_exercise' => $detail['rest_after_exercise'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('trainings.index')->with('success', 'トレーニングを更新しました！');
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
