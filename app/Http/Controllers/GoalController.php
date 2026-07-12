<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Goal;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    /**
     * 認証制限（ログインしていないと触れないようにする）
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
        // 選択された期間タイプ（デフォルトは 'weekly:週単位'）
        $type = $request->input('period_type', 'weekly');

        // ログインユーザーの指定された期間タイプの目標を、最新順で取得
        $goals = Auth::user()->goals()
            ->where('period_type', $type)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('goals.index', compact('goals', 'type'));
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
        $validated = $request->validate([
            'period_type'      => 'required|string|max:30',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'title'            => 'required|string|max:255',
            'action_plan'      => 'nullable|string',
        ]);

        // ログインユーザーのデータとして保存
        Auth::user()->goals()->create($validated);

        return redirect()->route('goals.index', ['period_type' => $request->period_type])
            ->with('status', '新しい目標を設定しました！PDCAを回していきましょう。');
    }

    /**
     * 振り返り（結果・感想・対策・コーチコメント）の編集画面
     */
    public function edit(Goal $goal)
    {
        // 本人以外の目標を編集できないように防御
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        return view('goals.edit', compact('goal'));
    }

    /**
     * 目標データ・振り返りデータの更新処理
     */
    public function update(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        // ⬇️ start_date と end_date も更新できるように追加
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
            'coach_comment'    => 'nullable|string',
        ]);

        $goal->update($validated);

        return redirect()->route('goals.index', ['period_type' => $goal->period_type])
            ->with('status', '目標設定および振り返り内容を更新しました！');
    }
}
