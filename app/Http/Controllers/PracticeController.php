<?php

namespace App\Http\Controllers;

use App\Practice;
use App\PracticeDetail;
use App\Services\PracticeVideos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class PracticeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            PracticeVideos::authorizePlayer(auth()->user()->targetPlayerId());
            if ($request->isMethod('post') || $request->isMethod('patch') || $request->isMethod('put')) {
                abort_unless((int) $request->input('player_id') === (int) auth()->user()->targetPlayerId(), 409,
                    '操作対象の選手が変わりました。画面を開き直してください。');
            }
            return $next($request);
        });
    }

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
            'details.*.video_url'     => 'nullable|array|max:' . config('practice_video.max_files'),
            'details.*.video_url.*'   => 'required|string|max:2048',
            'details.*.id' => 'nullable|integer',
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
                    ];

                    if ($user->isStaff()) {
                        $data['coach_rating'] = $detailData['coach_rating'] ?? null;
                        $data['feedback']     = $detailData['feedback'] ?? null;
                    }

                    $detail = $practice->details()->create($data);
                    PracticeVideos::attach($detail, $detailData['video_url'] ?? [], $targetPlayerId);
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
            'details.*.video_url'     => 'nullable|array|max:' . config('practice_video.max_files'),
            'details.*.video_url.*'   => 'required|string|max:2048',
            'details.*.id' => 'nullable|integer',
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

            // 明細をID単位で更新
            $submittedDetailIds = [];

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
                    ];

                    // スタッフだけコーチ側項目を更新
                    if ($user->isStaff()) {
                        $data['coach_rating'] = $detailData['coach_rating'] ?? null;
                        $data['feedback']     = $detailData['feedback'] ?? null;
                    }

                    // 既存明細
                    if (!empty($detailData['id'])) {

                        $practiceDetail = $practice->details()
                            ->where('id', $detailData['id'])
                            ->first();

                        abort_unless($practiceDetail, 422, '練習メニューが見つかりません。');
                        if ($practiceDetail) {

                            $practiceDetail->update($data);
                            PracticeVideos::attach($practiceDetail, $detailData['video_url'] ?? [], $targetPlayerId);

                            $submittedDetailIds[] = $practiceDetail->id;
                        }

                    } else {

                        // 新規明細
                        $practiceDetail = $practice->details()
                            ->create($data);

                        PracticeVideos::attach($practiceDetail, $detailData['video_url'] ?? [], $targetPlayerId);
                        $submittedDetailIds[] = $practiceDetail->id;
                    }
                }
            }

            // 画面上で削除された明細だけ削除
            if (!empty($submittedDetailIds)) {

                $practice->details()
                    ->whereNotIn('id', $submittedDetailIds)
                    ->delete();

            } else {

                $practice->details()->delete();
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

        DB::transaction(function () use ($practice) {
            $practice->details()->delete();
            $practice->delete();
        });

        return redirect()
            ->route('practices.index')
            ->with('status', '練習記録を削除しました。');
    }
}
