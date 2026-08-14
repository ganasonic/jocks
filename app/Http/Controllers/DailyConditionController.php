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

        /*
        * ============================================================
        * グラフ用データ
        * ============================================================
        */
        $graphLabels = $graphData
            ->pluck('date')
            ->toArray();

        // 体温
        $graphTemperatures = $graphData
            ->pluck('body_temperature')
            ->toArray();

        // 体調レベル
        $graphConditions = $graphData
            ->pluck('condition_level')
            ->toArray();

        // 体重
        $graphWeights = $graphData
            ->pluck('body_weight')
            ->toArray();

        // 安静時心拍数
        $graphHeartRates = $graphData
            ->pluck('resting_heart_rate')
            ->toArray();

        // 最高血圧（収縮期）
        $graphSystolic = $graphData
            ->pluck('systolic_blood_pressure')
            ->toArray();

        // 最低血圧（拡張期）
        $graphDiastolic = $graphData
            ->pluck('diastolic_blood_pressure')
            ->toArray();

        // 血中酸素飽和度
        $graphSpo2 = $graphData
            ->pluck('spo2')
            ->toArray();

        return view('conditions.index', compact(
            'calendarWeeks',
            'year',
            'month',
            'prevMonth',
            'nextMonth',

            'graphLabels',

            'graphTemperatures',
            'graphConditions',

            'graphWeights',

            'graphHeartRates',
            'graphSystolic',
            'graphDiastolic',

            'graphSpo2'
        ));
    }

    /**
     * 新規入力画面
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $targetUser = \App\User::findOrFail($targetPlayerId);

        return view('conditions.create', [
            'date'       => $request->input('date'),
            'targetUser' => $targetUser,
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
            'date' => 'required|date',
            // 身体データ
            'body_temperature' => 'nullable|numeric|between:35.0,42.0',
            'body_weight' => 'nullable|numeric|between:20,250',
            'resting_heart_rate' => 'nullable|integer|between:30,220',
            'systolic_blood_pressure' => 'nullable|integer|between:60,250',
            'diastolic_blood_pressure' => 'nullable|integer|between:30,150',
            'spo2' => 'nullable|integer|between:70,100',
            // コンディション
            'condition_level' => 'required|integer|between:1,5',
            'mood_level' => 'required|integer|between:1,5',
            'wakeup_time' => 'nullable|date_format:H:i',
            'bedtime' => 'nullable|date_format:H:i',
            'meals_memo' => 'nullable|string|max:1000',
            // 女性コンディション
            'menstruation' => 'nullable|boolean',
            'menstruation_start_date' => 'nullable|date',
            'menstruation_condition' => 'nullable|integer|between:1,5',
            'menstruation_memo' => 'nullable|string|max:1000',

        ]);

        $targetUser = \App\User::findOrFail($targetPlayerId);

        $isFemale = $targetUser->userDetail
            && $targetUser->userDetail->gender === 'female';

        if (!$isFemale) {
            $validated['menstruation'] = null;
            $validated['menstruation_start_date'] = null;
            $validated['menstruation_condition'] = null;
            $validated['menstruation_memo'] = null;
        }

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

        $targetUser = \App\User::findOrFail($targetPlayerId);

        return view('conditions.edit', compact(
            'condition',
            'targetUser'
        ));
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
            // 身体データ
            'body_temperature' => 'nullable|numeric|between:35.0,42.0',
            'body_weight' => 'nullable|numeric|between:20,250',
            'resting_heart_rate' => 'nullable|integer|between:30,220',
            'systolic_blood_pressure' => 'nullable|integer|between:60,250',
            'diastolic_blood_pressure' => 'nullable|integer|between:30,150',
            'spo2' => 'nullable|integer|between:70,100',
            // コンディション
            'condition_level' => 'required|integer|between:1,5',
            'mood_level' => 'required|integer|between:1,5',
            'wakeup_time' => 'nullable|date_format:H:i',
            'bedtime' => 'nullable|date_format:H:i',
            'meals_memo' => 'nullable|string|max:1000',
            // 女性コンディション
            'menstruation' => 'nullable|boolean',
            'menstruation_start_date' => 'nullable|date',
            'menstruation_condition' => 'nullable|integer|between:1,5',
            'menstruation_memo' => 'nullable|string|max:1000',
            // スタッフ
            'feedback' => 'nullable|string|max:2000',
        ]);

        $targetUser = \App\User::findOrFail($targetPlayerId);

        $isFemale = $targetUser->userDetail
            && $targetUser->userDetail->gender === 'female';

        if (!$isFemale) {
            $validated['menstruation'] = null;
            $validated['menstruation_start_date'] = null;
            $validated['menstruation_condition'] = null;
            $validated['menstruation_memo'] = null;
        }

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
