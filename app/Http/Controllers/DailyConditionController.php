<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DailyCondition;
use Illuminate\Support\Facades\Auth;

class DailyConditionController extends Controller
{
    /**
     * ログイン必須
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 体調記録の一覧画面
     */
    public function indexlist(Request $request)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $conditions = DailyCondition::where('user_id', $targetPlayerId)
            ->orderBy('date', 'desc')
            ->get();

        return view('conditions.indexlist', compact('conditions'));
    }

    /**
     * カレンダー ＆ グラフ表示
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));

        $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

        $conditions = DailyCondition::where('user_id', $targetPlayerId)
            ->whereBetween('date', [
                $firstDay->format('Y-m-d'),
                $lastDay->format('Y-m-d')
            ])
            ->get()
            ->keyBy('date');

        $calendarWeeks = [];
        $week = [];

        for ($i = 0; $i < $firstDay->dayOfWeek; $i++) {
            $week[] = null;
        }

        for ($day = 1; $day <= $lastDay->day; $day++) {
            $currentDateStr = \Carbon\Carbon::createFromDate(
                $year,
                $month,
                $day
            )->format('Y-m-d');

            $week[] = [
                'day'  => $day,
                'date' => $currentDateStr,
                'data' => $conditions->get($currentDateStr),
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

        /*
         * グラフ用
         * 対象ユーザーの直近30件
         */
        $graphData = DailyCondition::where('user_id', $targetPlayerId)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get()
            ->sortBy('date')
            ->values();

        $graphLabels = $graphData->pluck('date')->toArray();
        $graphTemperatures = $graphData
            ->pluck('body_temperature')
            ->toArray();
        $graphConditions = $graphData
            ->pluck('condition_level')
            ->toArray();

        return view('conditions.index', compact(
            'calendarWeeks',
            'year',
            'month',
            'prevMonth',
            'nextMonth',
            'graphLabels',
            'graphTemperatures',
            'graphConditions'
        ));
    }

    /**
     * 新規入力画面
     */
    public function create(Request $request)
    {
        return view('conditions.create', [
            'date' => $request->input('date'),
        ]);
    }

    /**
     * 新規保存
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $validated = $request->validate([
            'date'             => 'required|date',
            'body_temperature' => 'nullable|numeric|between:35.0,42.0',
            'condition_level'  => 'required|integer|between:1,5',
            'mood_level'       => 'required|integer|between:1,5',
            'wakeup_time'      => 'nullable|date_format:H:i',
            'bedtime'          => 'nullable|date_format:H:i',
            'meals_memo'       => 'nullable|string|max:1000',
        ]);

        /*
         * 同じ対象ユーザー・同じ日付の重複防止
         */
        $exists = DailyCondition::where('user_id', $targetPlayerId)
            ->where('date', $validated['date'])
            ->exists();

        if ($exists) {
            return redirect()
                ->route('conditions.edit', [
                    'date' => $validated['date']
                ])
                ->with(
                    'status',
                    'この日の体調記録はすでに登録されています。'
                );
        }

        $validated['user_id'] = $targetPlayerId;

        DailyCondition::create($validated);

        return redirect()
            ->route('conditions.index')
            ->with('status', '本日の体調を記録しました！');
    }

    /**
     * 編集画面
     */
    public function edit($date)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $condition = DailyCondition::where('user_id', $targetPlayerId)
            ->where('date', $date)
            ->firstOrFail();

        return view('conditions.edit', compact('condition'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, $date)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $condition = DailyCondition::where('user_id', $targetPlayerId)
            ->where('date', $date)
            ->firstOrFail();

        $validated = $request->validate([
            'body_temperature' => 'nullable|numeric|between:35.0,42.0',
            'condition_level'  => 'required|integer|between:1,5',
            'mood_level'       => 'required|integer|between:1,5',
            'wakeup_time'      => 'nullable|date_format:H:i',
            'bedtime'          => 'nullable|date_format:H:i',
            'meals_memo'       => 'nullable|string|max:1000',
            'feedback'         => 'nullable|string|max:2000',
        ]);

        /*
        * スタッフだけフィードバックを更新できる
        *
        * 選手本人が更新した場合はfeedbackをupdate対象から
        * 外すため、既存コメントはそのまま残る。
        */
        if ($user->isStaff()) {
            $validated['feedback_by'] = $user->id;
        } else {
            unset($validated['feedback']);
        }

        $condition->update($validated);

        return redirect()
            ->route('conditions.index')
            ->with(
                'status',
                $date . ' の体調記録を更新しました！'
            );
    }
}
