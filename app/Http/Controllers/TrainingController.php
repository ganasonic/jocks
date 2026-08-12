<?php

namespace App\Http\Controllers;

use Auth;

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
        $user = auth()->user();

        $userId = $user->targetPlayerId();

        // 1. トレーニングのメインデータを保存
        $training = \App\Training::create([
            'user_id'       => $userId,
            'created_by'    => auth()->id(),
            'updated_by'    => auth()->id(),
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
        $user = auth()->user();

        $targetPlayerId = $user->targetPlayerId();

        // 現在操作対象の選手のデータでなければ編集禁止
        if (!$targetPlayerId || $training->user_id != $targetPlayerId) {
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
        $user = auth()->user();

        $targetPlayerId = $user->targetPlayerId();

        if (!$targetPlayerId || $training->user_id != $targetPlayerId) {
            abort(403);
        }

        // 1. メインデータの更新
        $updateData = [
            'title'         => $request->title ?? 'ワークアウト',
            'training_date' => $request->training_date,
            'updated_by'    => auth()->id(),
        ];

        // フィードバックはスタッフだけ更新可能
        if (auth()->user()->isStaff()) {
            $updateData['feedback'] = $request->feedback;
        }

        $training->update($updateData);

        // 2. 明細をID単位で更新
        $submittedDetailIds = [];

        if ($request->has('details')) {

            foreach ($request->details as $detail) {

                if (empty($detail['exercise_name'])) {
                    continue;
                }

                $detailData = [
                    'exercise_name'       => $detail['exercise_name'],
                    'weight'              => $detail['weight'] ?? null,
                    'reps'                => $detail['reps'] ?? null,
                    'sets'                => $detail['sets'] ?? null,
                    'interval'            => $detail['interval'] ?? null,
                    'rpe'                 => $detail['rpe'] ?? null,
                    'rest_after_exercise' => $detail['rest_after_exercise'] ?? null,
                ];

                // スタッフだけfeedbackを更新できる
                if ($user->isStaff()) {
                    $detailData['feedback'] = $detail['feedback'] ?? null;
                }

                // 既存明細
                if (!empty($detail['id'])) {

                    $trainingDetail = $training->details()
                        ->where('id', $detail['id'])
                        ->first();

                    if ($trainingDetail) {

                        $trainingDetail->update($detailData);

                        $submittedDetailIds[] = $trainingDetail->id;
                    }

                } else {

                    // 新規明細
                    $trainingDetail = $training->details()
                        ->create($detailData);

                    $submittedDetailIds[] = $trainingDetail->id;
                }
            }
        }

        // 画面から削除された明細だけDBから削除
        if (!empty($submittedDetailIds)) {

            $training->details()
                ->whereNotIn('id', $submittedDetailIds)
                ->delete();

        } else {

            // 明細が全部削除された場合
            $training->details()->delete();
        }

        return redirect()->route('trainings.index')->with('success', 'トレーニングを更新しました！');
    }

    public function index()
    {
        $user = Auth::user();

        $targetPlayerId = $user->targetPlayerId();

        // ユーザーのトレーニング記録を日付順（新しい順）で取得
        $trainings = \App\Training::where('user_id', $targetPlayerId)
            ->orderBy('training_date', 'desc')
            ->with('details') // 詳細データも一緒に取得
            ->get();

        return view('trainings.index', compact('trainings'));
    }
}
