<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DailyCondition;
use Illuminate\Support\Facades\Auth;

class DailyConditionController extends Controller
{
    /**
     * ログイン必須にするためのコンストラクタ
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 体調記録の一覧画面を表示
     */
    public function indexlist(Request $request)
    {
        // ログイン中のユーザーの体調データを、日付が新しい順に取得
        $conditions = Auth::user()->dailyConditions()->orderBy('date', 'desc')->get();

        return view('conditions.index', compact('conditions'));
    }

    /**
     * 体調記録の一覧画面（カレンダー ＆ グラフ表示）
     */
    public function index(Request $request)
    {
        // --- 1. カレンダー用の処理 (既存) ---
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));

        $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

        $conditions = Auth::user()->dailyConditions()
            ->whereBetween('date', [$firstDay->format('Y-m-d'), $lastDay->format('Y-m-d')])
            ->get()
            ->keyBy('date');

        $calendarWeeks = [];
        $week = [];

        for ($i = 0; $i < $firstDay->dayOfWeek; $i++) {
            $week[] = null;
        }

        for ($day = 1; $day <= $lastDay->day; $day++) {
            $currentDateStr = \Carbon\Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            $week[] = [
                'day' => $day,
                'date' => $currentDateStr,
                'data' => $conditions->get($currentDateStr)
            ];

            if (count($week) == 7) {
                $calendarWeeks[] = $week;
                $week = [];
            }
        }

        while (count($week) > 0 && count($week) < 7) {
            $week[] = null;
        }
        if (count($week) > 0) {
            $calendarWeeks[] = $week;
        }

        $prevMonth = $firstDay->copy()->subMonth();
        $nextMonth = $firstDay->copy()->addMonth();


        // --- 2. グラフ用のデータ処理 (ここから追記) ---
        // 直近30日間のデータを「日付の古い順（昇順）」に取得
        $graphData = Auth::user()->dailyConditions()
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        // JavaScriptに渡すために配列の形に変換
        $graphLabels = $graphData->pluck('date')->toArray(); // X軸：日付
        $graphTemperatures = $graphData->pluck('body_temperature')->toArray(); // Y軸1：体温
        $graphConditions = $graphData->pluck('condition_level')->toArray(); // Y軸2：体調

        return view('conditions.index', compact(
            'calendarWeeks', 'year', 'month', 'prevMonth', 'nextMonth',
            'graphLabels', 'graphTemperatures', 'graphConditions'
        ));
    }

    /**
     * 体調記録の入力画面を表示
     */
    public function create()
    {
        return view('conditions.create');
    }

    /**
     * 入力された体調データをデータベースに保存
     */
    public function store(Request $request)
    {
        // 入力データのバリデーション（チェック）
        $validated = $request->validate([
            'date' => 'required|date',
            'body_temperature' => 'nullable|numeric|between:30.00,45.00',
            'condition_level' => 'required|integer|between:1,5',
            'mood_level' => 'required|integer|between:1,5',
            'wakeup_time' => 'nullable|date_format:H:i',
            'bedtime' => 'nullable|date_format:H:i',
            'meals_memo' => 'nullable|string|max:1000',
        ]);

        // ログインユーザーのデータとして保存
        Auth::user()->dailyConditions()->create($validated);

        return redirect()->route('conditions.index')
            ->with('status', '本日の体調を記録しました！');
    }

    /**
     * 体調記録の編集画面を表示
     */
    public function edit($date)
    {
        // 指定された日付のデータを取得（なければ404エラー）
        $condition = Auth::user()->dailyConditions()->where('date', $date)->firstOrFail();

        return view('conditions.edit', compact('condition'));
    }

    /**
     * 体調記録を更新
     */
    public function update(Request $request, $date)
    {
        // 該当データの取得
        $condition = Auth::user()->dailyConditions()->where('date', $date)->firstOrFail();

        // バリデーション（store時とほぼ同じ）
        $validated = $request->validate([
            'body_temperature' => 'nullable|numeric|between:35.0,42.0',
            'condition_level'  => 'required|integer|between:1,5',
            'mood_level'       => 'required|integer|between:1,5',
            'wakeup_time'      => 'nullable',
            'bedtime'          => 'nullable',
            'meals_memo'       => 'nullable|string|max:1000',
        ]);

        // データの更新
        $condition->update($validated);

        return redirect()->route('conditions.index')
            ->with('status', $date . ' の体調記録を更新しました！');
    }
}
