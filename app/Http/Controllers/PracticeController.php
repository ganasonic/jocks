<?php

namespace App\Http\Controllers;

use App\Practice;
use App\PracticeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PracticeController extends Controller
{
    /**
     * 一覧表示
     */
    public function index()
    {
        $practices = Practice::with('details')
            ->orderBy('practice_date', 'desc')
            ->paginate(10);

        return view('practices.index', compact('practices'));
    }

    /**
     * 新規作成画面
     */
    public function create()
    {
        // 過去に入力された種別・カテゴリの履歴（重複排除）
        $recentTypes = Practice::whereNotNull('practice_type')
            ->where('practice_type', '!=', '')
            ->pluck('practice_type')
            ->unique()
            ->values();

        // 過去に入力されたメニュー名の履歴（重複排除）
        $recentMenus = PracticeDetail::whereNotNull('menu_name')
            ->where('menu_name', '!=', '')
            ->pluck('menu_name')
            ->unique()
            ->values();

        return view('practices.create', compact('recentTypes', 'recentMenus'));
    }

    /**
     * 新規登録処理
     */
    public function store(Request $request)
    {
        $request->validate([
            'practice_date'           => 'required|date',
            'practice_type'           => 'nullable|string|max:255',
            'title'                   => 'nullable|string|max:255',
            'target'                  => 'nullable|string',
            'overall_coach_comment'   => 'nullable|string',
            'details'                 => 'nullable|array',
            'details.*.menu_name'     => 'required|string|max:255',
            'details.*.runs_or_time'  => 'nullable|string|max:255',
            'details.*.rating'        => 'nullable|integer|min:1|max:5',
            'details.*.coach_rating'  => 'nullable|integer|min:1|max:5',
            'details.*.impression'    => 'nullable|string',
            'details.*.notice'        => 'nullable|string',
            'details.*.coach_comment' => 'nullable|string',
            'details.*.video_url'     => 'nullable|url',
        ]);

        DB::transaction(function () use ($request) {
            $practice = Practice::create([
                'user_id'               => auth()->id() ?? 1,
                'practice_date'         => $request->practice_date,
                'practice_type'         => $request->practice_type,
                'title'                 => $request->title,
                'target'                => $request->target,
                'overall_coach_comment' => $request->overall_coach_comment,
            ]);

            if ($request->has('details')) {
                foreach ($request->details as $detailData) {
                    if (!empty($detailData['menu_name'])) {
                        $practice->details()->create([
                            'menu_name'     => $detailData['menu_name'],
                            'runs_or_time' => $detailData['runs_or_time'] ?? null,
                            'rating'       => $detailData['rating'] ?? null,
                            'coach_rating' => $detailData['coach_rating'] ?? null,
                            'impression'   => $detailData['impression'] ?? null,
                            'notice'       => $detailData['notice'] ?? null,
                            'coach_comment' => $detailData['coach_comment'] ?? null,
                            'video_url'    => $detailData['video_url'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('practices.index')->with('status', '練習記録を登録しました。');
    }

    /**
     * 詳細表示
     */
    public function show($id)
    {
        $practice = Practice::with('details')->findOrFail($id);
        return view('practices.show', compact('practice'));
    }

    /**
     * 編集画面
     */
    public function edit($id)
    {
        $practice = Practice::with('details')->findOrFail($id);

        $recentTypes = Practice::whereNotNull('practice_type')
            ->where('practice_type', '!=', '')
            ->pluck('practice_type')
            ->unique()
            ->values();

        $recentMenus = PracticeDetail::whereNotNull('menu_name')
            ->where('menu_name', '!=', '')
            ->pluck('menu_name')
            ->unique()
            ->values();

        return view('practices.edit', compact('practice', 'recentTypes', 'recentMenus'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'practice_date'           => 'required|date',
            'practice_type'           => 'nullable|string|max:255',
            'title'                   => 'nullable|string|max:255',
            'target'                  => 'nullable|string',
            'overall_coach_comment'   => 'nullable|string',
            'details'                 => 'nullable|array',
            'details.*.menu_name'     => 'required|string|max:255',
            'details.*.runs_or_time'  => 'nullable|string|max:255',
            'details.*.rating'        => 'nullable|integer|min:1|max:5',
            'details.*.coach_rating'  => 'nullable|integer|min:1|max:5',
            'details.*.impression'    => 'nullable|string',
            'details.*.notice'        => 'nullable|string',
            'details.*.coach_comment' => 'nullable|string',
            'details.*.video_url'     => 'nullable|url',
        ]);

        DB::transaction(function () use ($request, $id) {
            $practice = Practice::findOrFail($id);

            $practice->update([
                'practice_date'         => $request->practice_date,
                'practice_type'         => $request->practice_type,
                'title'                 => $request->title,
                'target'                => $request->target,
                'overall_coach_comment' => $request->overall_coach_comment,
            ]);

            // 一旦既存の明細を削除して全再作成
            $practice->details()->delete();

            if ($request->has('details')) {
                foreach ($request->details as $detailData) {
                    if (!empty($detailData['menu_name'])) {
                        $practice->details()->create([
                            'menu_name'     => $detailData['menu_name'],
                            'runs_or_time' => $detailData['runs_or_time'] ?? null,
                            'rating'       => $detailData['rating'] ?? null,
                            'coach_rating' => $detailData['coach_rating'] ?? null,
                            'impression'   => $detailData['impression'] ?? null,
                            'notice'       => $detailData['notice'] ?? null,
                            'coach_comment' => $detailData['coach_comment'] ?? null,
                            'video_url'    => $detailData['video_url'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('practices.index')->with('status', '練習記録を更新しました。');
    }

    /**
     * 削除処理
     */
    public function destroy($id)
    {
        $practice = Practice::findOrFail($id);
        $practice->delete();

        return redirect()->route('practices.index')->with('status', '練習記録を削除しました。');
    }
}
