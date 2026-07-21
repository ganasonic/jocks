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
        // ログインユーザーのデータのみに絞り込む
        $practices = Practice::where('user_id', auth()->id())
            ->with('details')
            ->orderBy('practice_date', 'desc')
            ->paginate(10);

        return view('practices.index', compact('practices'));
    }

    /**
     * 新規作成画面
     */
    public function create()
    {
        // ログインユーザーが過去に入力した種別・カテゴリの履歴（重複排除）
        $recentTypes = Practice::where('user_id', auth()->id())
            ->whereNotNull('practice_type')
            ->where('practice_type', '!=', '')
            ->pluck('practice_type')
            ->unique()
            ->values();

        // ログインユーザーが過去に入力したメニュー名の履歴（重複排除）
        // ※PracticeDetail側にもuser_idがあるか、あるいは親経由で絞り込む形にする必要がありますが、
        //   ここでは安全のため親（Practice）のuser_idで紐づくものに限定しています
        $recentMenus = PracticeDetail::whereHas('practice', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->whereNotNull('menu_name')
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
                'user_id'               => auth()->id(), // デフォルト値のフォールバックを廃止し、必ずログインIDを強制
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
                            'runs_or_time'  => $detailData['runs_or_time'] ?? null,
                            'rating'        => $detailData['rating'] ?? null,
                            'coach_rating'  => $detailData['coach_rating'] ?? null,
                            'impression'    => $detailData['impression'] ?? null,
                            'notice'        => $detailData['notice'] ?? null,
                            'coach_comment' => $detailData['coach_comment'] ?? null,
                            'video_url'     => $detailData['video_url'] ?? null,
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
        // 他人のデータを見られないよう、IDとuser_idの両方で取得（他人の場合は404）
        $practice = Practice::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('details')
            ->firstOrFail();

        return view('practices.show', compact('practice'));
    }

    /**
     * 編集画面
     */
    public function edit($id)
    {
        // 他人のデータを編集させない
        $practice = Practice::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('details')
            ->firstOrFail();

        $recentTypes = Practice::where('user_id', auth()->id())
            ->whereNotNull('practice_type')
            ->where('practice_type', '!=', '')
            ->pluck('practice_type')
            ->unique()
            ->values();

        $recentMenus = PracticeDetail::whereHas('practice', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->whereNotNull('menu_name')
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
            // 他人のデータを不正に更新されないよう絞り込み
            $practice = Practice::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

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
                            'runs_or_time'  => $detailData['runs_or_time'] ?? null,
                            'rating'        => $detailData['rating'] ?? null,
                            'coach_rating'  => $detailData['coach_rating'] ?? null,
                            'impression'    => $detailData['impression'] ?? null,
                            'notice'        => $detailData['notice'] ?? null,
                            'coach_comment' => $detailData['coach_comment'] ?? null,
                            'video_url'     => $detailData['video_url'] ?? null,
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
        // 他人のデータを勝手に削除されないよう絞り込み
        $practice = Practice::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $practice->delete();

        return redirect()->route('practices.index')->with('status', '練習記録を削除しました。');
    }
}
