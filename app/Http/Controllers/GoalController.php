<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Goal;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    /**
     * 認証制限
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 目標一覧画面
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 本人モードなら本人ID
        // 担当選手モードなら対象選手ID
        $targetPlayerId = $user->targetPlayerId();

        // 選択された期間タイプ
        $type = $request->input('period_type', 'weekly');

        $goals = Goal::where('user_id', $targetPlayerId)
            ->where('period_type', $type)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('goals.index', compact(
            'goals',
            'type'
        ));
    }

    /**
     * 目標登録画面
     */
    public function create()
    {
        return view('goals.create');
    }

    /**
     * 目標の新規保存処理
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $validated = $request->validate([
            'period_type' => 'required|string|max:30',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'title'       => 'required|string|max:255',
            'action_plan' => 'nullable|string',
        ]);

        // 対象ユーザーの目標として保存
        $validated['user_id'] = $targetPlayerId;

        Goal::create($validated);

        return redirect()
            ->route('goals.index', [
                'period_type' => $request->period_type
            ])
            ->with(
                'status',
                '新しい目標を設定しました！PDCAを回していきましょう。'
            );
    }

    /**
     * 振り返り編集画面
     */
    public function edit(Goal $goal)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        // 現在操作対象のユーザーの目標以外は編集禁止
        if ($goal->user_id != $targetPlayerId) {
            abort(403);
        }

        return view('goals.edit', compact('goal'));
    }

    /**
     * 目標データ・振り返りデータの更新処理
     */
    public function update(Request $request, Goal $goal)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        if ($goal->user_id != $targetPlayerId) {
            abort(403);
        }

        $validated = $request->validate([
            'period_type'      => 'required|string|max:30',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'title'            => 'required|string|max:255',
            'action_plan'      => 'nullable|string',
            'result_memo'      => 'nullable|string',
            'impression'       => 'nullable|string',
            'achievement_rate' => 'nullable|integer|between:0,100',
            'countermeasure'   => 'nullable|string',
            'next_action'      => 'nullable|string',

            // スタッフ用
            'feedback'         => 'nullable|string',
        ]);

        /*
         * 選手側が更新したとき、
         * 既存のスタッフfeedbackを消さないようにする
         */
        if (!$user->isStaff()) {
            unset($validated['feedback']);
        }

        /*
         * スタッフがfeedbackを更新した場合、
         * 誰が入力したか保存
         */
        if ($user->isStaff()) {
            $validated['coach_id'] = $user->id;
        }

        $goal->update($validated);

        return redirect()
            ->route('goals.index', [
                'period_type' => $goal->period_type
            ])
            ->with(
                'status',
                '目標設定および振り返り内容を更新しました！'
            );
    }
}
