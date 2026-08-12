<?php

namespace App\Http\Controllers;

use App\Practice;
use App\PracticeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class PracticeController extends Controller
{
    /**
     * 一覧表示
     */
    public function index()
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $practices = Practice::where('user_id', $targetPlayerId)
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
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $recentTypes = Practice::where('user_id', $targetPlayerId)
            ->whereNotNull('practice_type')
            ->where('practice_type', '!=', '')
            ->pluck('practice_type')
            ->unique()
            ->values();

        $recentMenus = PracticeDetail::whereHas('practice', function ($query) use ($targetPlayerId) {
                $query->where('user_id', $targetPlayerId);
            })
            ->whereNotNull('menu_name')
            ->where('menu_name', '!=', '')
            ->pluck('menu_name')
            ->unique()
            ->values();

        return view('practices.create', compact(
            'recentTypes',
            'recentMenus'
        ));
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
            'feedback'                => 'nullable|string',
            'details'                 => 'nullable|array',
            'details.*.menu_name'     => 'required|string|max:255',
            'details.*.runs_or_time'  => 'nullable|string|max:255',
            'details.*.rating'        => 'nullable|integer|min:1|max:5',
            'details.*.coach_rating'  => 'nullable|integer|min:1|max:5',
            'details.*.impression'    => 'nullable|string',
            'details.*.notice'        => 'nullable|string',
            'details.*.feedback'      => 'nullable|string',
            'details.*.video_url'     => 'nullable|url',
        ]);

        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        DB::transaction(function () use ($request, $user, $targetPlayerId) {

            $practiceData = [
                'user_id'       => $targetPlayerId,
                'created_by'    => $user->id,
                'updated_by'    => $user->id,
                'practice_date' => $request->practice_date,
                'practice_type' => $request->practice_type,
                'title'         => $request->title,
                'target'        => $request->target,
            ];

            // スタッフだけフィードバック登録可能
            if ($user->isStaff()) {
                $practiceData['feedback'] = $request->feedback;
            }

            $practice = Practice::create($practiceData);

            if ($request->has('details')) {

                foreach ($request->details as $detailData) {

                    if (empty($detailData['menu_name'])) {
                        continue;
                    }

                    $data = [
                        'menu_name'    => $detailData['menu_name'],
                        'runs_or_time' => $detailData['runs_or_time'] ?? null,
                        'rating'       => $detailData['rating'] ?? null,
                        'impression'   => $detailData['impression'] ?? null,
                        'notice'       => $detailData['notice'] ?? null,
                        'video_url'    => $detailData['video_url'] ?? null,
                    ];

                    if ($user->isStaff()) {
                        $data['coach_rating'] = $detailData['coach_rating'] ?? null;
                        $data['feedback']     = $detailData['feedback'] ?? null;
                    }

                    $practice->details()->create($data);
                }
            }
        });

        return redirect()
            ->route('practices.index')
            ->with('status', '練習記録を登録しました。');
    }

    /**
     * 詳細表示
     */
    public function show($id)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $practice = Practice::where('id', $id)
            ->where('user_id', $targetPlayerId)
            ->with('details')
            ->firstOrFail();

        return view('practices.show', compact('practice'));
    }

    /**
     * 編集画面
     */
    public function edit($id)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $practice = Practice::where('id', $id)
            ->where('user_id', $targetPlayerId)
            ->with('details')
            ->firstOrFail();

        $recentTypes = Practice::where('user_id', $targetPlayerId)
            ->whereNotNull('practice_type')
            ->where('practice_type', '!=', '')
            ->pluck('practice_type')
            ->unique()
            ->values();

        $recentMenus = PracticeDetail::whereHas('practice', function ($query) use ($targetPlayerId) {
                $query->where('user_id', $targetPlayerId);
            })
            ->whereNotNull('menu_name')
            ->where('menu_name', '!=', '')
            ->pluck('menu_name')
            ->unique()
            ->values();

        return view('practices.edit', compact(
            'practice',
            'recentTypes',
            'recentMenus'
        ));
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
            'feedback'                => 'nullable|string',
            'details'                 => 'nullable|array',
            'details.*.menu_name'     => 'required|string|max:255',
            'details.*.runs_or_time'  => 'nullable|string|max:255',
            'details.*.rating'        => 'nullable|integer|min:1|max:5',
            'details.*.coach_rating'  => 'nullable|integer|min:1|max:5',
            'details.*.impression'    => 'nullable|string',
            'details.*.notice'        => 'nullable|string',
            'details.*.feedback'      => 'nullable|string',
            'details.*.video_url'     => 'nullable|url',
        ]);

        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        DB::transaction(function () use ($request, $id, $user, $targetPlayerId) {

            $practice = Practice::where('id', $id)
                ->where('user_id', $targetPlayerId)
                ->firstOrFail();

            $practiceData = [
                'practice_date' => $request->practice_date,
                'practice_type' => $request->practice_type,
                'title'         => $request->title,
                'target'        => $request->target,
                'updated_by'    => $user->id,
            ];

            if ($user->isStaff()) {
                $practiceData['feedback'] = $request->feedback;
            }

            $practice->update($practiceData);

            /*
             * いまは既存方式を維持
             * 後で training_details と同様に
             * ID単位更新方式へ変更した方が安全
             */
            $practice->details()->delete();

            if ($request->has('details')) {

                foreach ($request->details as $detailData) {

                    if (empty($detailData['menu_name'])) {
                        continue;
                    }

                    $data = [
                        'menu_name'    => $detailData['menu_name'],
                        'runs_or_time' => $detailData['runs_or_time'] ?? null,
                        'rating'       => $detailData['rating'] ?? null,
                        'impression'   => $detailData['impression'] ?? null,
                        'notice'       => $detailData['notice'] ?? null,
                        'video_url'    => $detailData['video_url'] ?? null,
                    ];

                    if ($user->isStaff()) {
                        $data['coach_rating'] = $detailData['coach_rating'] ?? null;
                        $data['feedback']     = $detailData['feedback'] ?? null;
                    }

                    $practice->details()->create($data);
                }
            }
        });

        return redirect()
            ->route('practices.index')
            ->with('status', '練習記録を更新しました。');
    }

    /**
     * 削除処理
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $practice = Practice::where('id', $id)
            ->where('user_id', $targetPlayerId)
            ->firstOrFail();

        $practice->delete();

        return redirect()
            ->route('practices.index')
            ->with('status', '練習記録を削除しました。');
    }
}
