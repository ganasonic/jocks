<?php

namespace App\Http\Controllers;

use App\Nutrition;
use App\NutritionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Auth;

class NutritionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 一覧
     */
    public function index()
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $nutritions = Nutrition::where('user_id', $targetPlayerId)
            ->orderBy('nutrition_date', 'desc')
            ->get();

        return view('nutritions.index', compact('nutritions'));
    }

    /**
     * 新規登録画面
     */
    public function create()
    {
        return view('nutritions.create');
    }

    /**
     * 新規登録
     */
    public function store(Request $request)
    {
        $request->validate([
            'nutrition_date' => 'required|date',
            'meal_type'      => 'required|string',
            'photo'          => 'nullable|image|max:2048',
            'daily_memo'     => 'nullable|string',
            'calories'       => 'nullable|integer|min:0',
            'protein'        => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        DB::transaction(function () use ($request, $user, $targetPlayerId) {

            $nutrition = Nutrition::create([
                'user_id'        => $targetPlayerId,
                'created_by'     => $user->id,
                'updated_by'     => $user->id,
                'nutrition_date' => $request->nutrition_date,
                'daily_memo'     => $request->daily_memo,
            ]);

            $photoPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')
                    ->store('photos', 'public');
            }

            $nutrition->details()->create([
                'meal_type'  => $request->meal_type,
                'photo_path' => $photoPath,
                'calories'   => $request->calories,
                'protein'    => $request->protein,
            ]);
        });

        return redirect()
            ->route('nutritions.index')
            ->with('success', '登録しました');
    }

    /**
     * 詳細
     */
    public function show($id)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $nutrition = Nutrition::where('user_id', $targetPlayerId)
            ->with('details')
            ->findOrFail($id);

        return view('nutritions.show', compact('nutrition'));
    }

    /**
     * 編集
     */
    public function edit($id)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $nutrition = Nutrition::where('user_id', $targetPlayerId)
            ->with('details')
            ->findOrFail($id);

        return view('nutritions.edit', compact('nutrition'));
    }

    /**
     * 更新
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $nutrition = Nutrition::where('user_id', $targetPlayerId)
            ->with('details')
            ->findOrFail($id);

        DB::transaction(function () use ($request, $nutrition, $user) {

            /*
             * 親データ
             */
            $nutritionData = [
                'nutrition_date' => $request->nutrition_date,
                'daily_memo'     => $request->daily_memo,
                'updated_by'     => $user->id,
            ];

            // feedbackはスタッフだけ更新
            if ($user->isStaff()) {
                $nutritionData['feedback'] = $request->feedback;
            }

            $nutrition->update($nutritionData);

            /*
             * 既存の食事詳細
             *
             * 既にID付きで送られてくるため、
             * 全削除→再作成は行わず、そのレコードをupdateする。
             */
            if ($request->has('details')) {

                foreach ($request->details as $detailId => $data) {

                    $detail = $nutrition->details()
                        ->where('id', $detailId)
                        ->firstOrFail();

                    $detailData = [
                        'meal_type' => $data['meal_type'] ?? $detail->meal_type,
                        'calories'  => $data['calories'] ?? null,
                        'protein'   => $data['protein'] ?? null,
                        'meal_memo' => $data['meal_memo'] ?? null,
                    ];

                    /*
                     * 選手が編集したときに
                     * スタッフfeedbackを消さない
                     */
                    if ($user->isStaff()) {
                        $detailData['feedback'] = $data['feedback'] ?? null;
                    }

                    /*
                     * 写真が差し替えられた場合
                     */
                    if (isset($data['photo']) && $data['photo']) {

                        if ($detail->photo_path) {
                            Storage::disk('public')
                                ->delete($detail->photo_path);
                        }

                        $detailData['photo_path'] =
                            $data['photo']->store('photos', 'public');
                    }

                    $detail->update($detailData);
                }
            }

            /*
             * 同一編集画面からnew_detailを使っている場合
             */
            if ($request->filled('new_detail.meal_type')) {

                $newDetail = $request->new_detail;

                $path = null;

                if ($request->hasFile('new_detail.photo')) {
                    $path = $request->file('new_detail.photo')
                        ->store('photos', 'public');
                }

                $detailData = [
                    'meal_type'  => $newDetail['meal_type'],
                    'photo_path' => $path,
                    'calories'   => $newDetail['calories'] ?? null,
                    'protein'    => $newDetail['protein'] ?? null,
                    'meal_memo'  => $newDetail['meal_memo'] ?? null,
                ];

                if ($user->isStaff()) {
                    $detailData['feedback'] =
                        $newDetail['feedback'] ?? null;
                }

                $nutrition->details()->create($detailData);
            }
        });

        return redirect()
            ->route('nutritions.index')
            ->with('success', '更新しました');
    }

    /**
     * 1日分を削除
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $nutrition = Nutrition::where('user_id', $targetPlayerId)
            ->with('details')
            ->findOrFail($id);

        DB::transaction(function () use ($nutrition) {

            foreach ($nutrition->details as $detail) {

                if ($detail->photo_path) {
                    Storage::disk('public')
                        ->delete($detail->photo_path);
                }
            }

            $nutrition->details()->delete();
            $nutrition->delete();
        });

        return redirect()
            ->route('nutritions.index')
            ->with('success', '削除しました');
    }

    /**
     * 食事詳細削除
     */
    public function destroyDetail($id)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $detail = NutritionDetail::with('nutrition')
            ->findOrFail($id);

        if ($detail->nutrition->user_id != $targetPlayerId) {
            abort(403);
        }

        if ($detail->photo_path) {
            Storage::disk('public')
                ->delete($detail->photo_path);
        }

        $detail->delete();

        return back()
            ->with('success', '食事詳細を削除しました');
    }

    /**
     * 食事追加画面
     */
    public function createDetail($nutritionId)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $nutrition = Nutrition::where('user_id', $targetPlayerId)
            ->findOrFail($nutritionId);

        return view(
            'nutritions.details.create',
            compact('nutrition')
        );
    }

    /**
     * 食事追加保存
     */
    public function storeDetail(Request $request, $nutritionId)
    {
        $user = Auth::user();
        $targetPlayerId = $user->targetPlayerId();

        $nutrition = Nutrition::where('user_id', $targetPlayerId)
            ->findOrFail($nutritionId);

        $detail = new NutritionDetail();

        $detail->nutrition_id = $nutrition->id;
        $detail->meal_type = $request->meal_type;
        $detail->calories = $request->calories;
        $detail->protein = $request->protein;
        $detail->meal_memo = $request->meal_memo;

        if ($request->hasFile('photo')) {

            $path = $request->file('photo')
                ->store('nutritions', 'public');

            $detail->photo_path = $path;
        }

        $detail->save();

        /*
         * 親側も「誰が最後に編集したか」更新
         */
        $nutrition->update([
            'updated_by' => $user->id,
        ]);

        return redirect()
            ->route('nutritions.edit', $nutritionId)
            ->with('success', '食事を追加しました');
    }
}
