<?php

namespace App\Http\Controllers;

use App\Nutrition;
use App\NutritionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NutritionController extends Controller
{
    public function index()
    {
        $nutritions = Nutrition::orderBy('nutrition_date', 'desc')->get();
        return view('nutritions.index', compact('nutritions'));
    }

    public function create()
    {
        return view('nutritions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nutrition_date' => 'required|date',
            'meal_type' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $nutrition = Nutrition::create([
                'user_id' => auth()->id(),
                'nutrition_date' => $request->nutrition_date,
                'daily_memo' => $request->daily_memo,
            ]);

            $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('photos', 'public') : null;

            $nutrition->details()->create([
                'meal_type' => $request->meal_type,
                'photo_path' => $photoPath,
                'calories' => $request->calories,
                'protein' => $request->protein,
            ]);
        });

        return redirect()->route('nutritions.index')->with('success', '登録しました');
    }

    // 追加：showメソッド
    public function show($id)
    {
        $nutrition = Nutrition::with('details')->findOrFail($id);
        return view('nutritions.show', compact('nutrition'));
    }

    public function edit($id)
    {
        $nutrition = Nutrition::with('details')->findOrFail($id);
        return view('nutritions.edit', compact('nutrition'));
    }

    public function update(Request $request, $id)
    {
        $nutrition = Nutrition::findOrFail($id);

        DB::transaction(function () use ($request, $nutrition) {
            $nutrition->update($request->only(['nutrition_date', 'daily_memo']));

            if ($request->has('details')) {
                foreach ($request->details as $detailId => $data) {
                    $detail = $nutrition->details()->findOrFail($detailId);
                    if (isset($data['photo'])) {
                        if ($detail->photo_path) Storage::disk('public')->delete($detail->photo_path);
                        $data['photo_path'] = $data['photo']->store('photos', 'public');
                    }
                    $detail->update(array_diff_key($data, ['photo' => '']));
                }
            }

            if ($request->filled('new_detail.meal_type')) {
                $path = $request->hasFile('new_detail.photo') ? $request->file('new_detail.photo')->store('photos', 'public') : null;
                $nutrition->details()->create(array_merge($request->new_detail, ['photo_path' => $path]));
            }
        });

        return redirect()->route('nutritions.index')->with('success', '更新しました');
    }

    public function destroy($id)
    {
        $nutrition = \App\Nutrition::findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($nutrition) {
            // 関連する詳細データの写真をストレージから削除
            foreach ($nutrition->details as $detail) {
                if ($detail->photo_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($detail->photo_path);
                }
            }
            // データベースの関連レコードを削除
            $nutrition->details()->delete();
            $nutrition->delete();
        });

        return redirect()->route('nutritions.index')->with('success', '削除しました');
    }

    public function destroyDetail($id)
    {
        $detail = \App\NutritionDetail::findOrFail($id);

        // 写真がある場合は削除
        if ($detail->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($detail->photo_path);
        }

        $detail->delete();

        return back()->with('success', '食事詳細を削除しました');
    }

    // 新規追加画面を表示
    public function createDetail($nutritionId)
    {
        $nutrition = Nutrition::findOrFail($nutritionId);
        return view('nutritions.details.create', compact('nutrition'));
    }

    // 新規追加データを保存
    public function storeDetail(Request $request, $nutritionId)
    {
        // データの保存処理を記述
        $detail = new \App\NutritionDetail();
        $detail->nutrition_id = $nutritionId;
        $detail->meal_type = $request->meal_type;
        $detail->calories = $request->calories;
        $detail->protein = $request->protein;
        $detail->meal_memo = $request->meal_memo;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('nutritions', 'public');
            $detail->photo_path = $path;
        }

        $detail->save();

        return redirect()->route('nutritions.edit', $nutritionId)->with('success', '食事を追加しました');
    }
}
