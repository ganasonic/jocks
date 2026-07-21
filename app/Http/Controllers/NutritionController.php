<?php

namespace App\Http\Controllers;

use App\Nutrition;
use App\NutritionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NutritionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 修正: ログインユーザーのデータのみに絞り込む
        $nutritions = Nutrition::where('user_id', auth()->id())
            ->orderBy('nutrition_date', 'desc')
            ->get();

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

    public function show($id)
    {
        // 修正: 他人のデータを見られないようにチェック
        $nutrition = Nutrition::where('user_id', auth()->id())->with('details')->findOrFail($id);
        return view('nutritions.show', compact('nutrition'));
    }

    public function edit($id)
    {
        // 修正: 他人のデータを編集できないようにチェック
        $nutrition = Nutrition::where('user_id', auth()->id())->with('details')->findOrFail($id);
        return view('nutritions.edit', compact('nutrition'));
    }

    public function update(Request $request, $id)
    {
        // 修正: 他人のデータを更新できないようにチェック
        $nutrition = Nutrition::where('user_id', auth()->id())->findOrFail($id);

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
        // 修正: 他人のデータを削除できないようにチェック
        $nutrition = Nutrition::where('user_id', auth()->id())->findOrFail($id);

        DB::transaction(function () use ($nutrition) {
            foreach ($nutrition->details as $detail) {
                if ($detail->photo_path) {
                    Storage::disk('public')->delete($detail->photo_path);
                }
            }
            $nutrition->details()->delete();
            $nutrition->delete();
        });

        return redirect()->route('nutritions.index')->with('success', '削除しました');
    }

    public function destroyDetail($id)
    {
        $detail = NutritionDetail::with('nutrition')->findOrFail($id);

        // 所属するNutritionのuser_idが一致しているか確認
        if ($detail->nutrition->user_id !== auth()->id()) {
            abort(403);
        }

        if ($detail->photo_path) {
            Storage::disk('public')->delete($detail->photo_path);
        }

        $detail->delete();

        return back()->with('success', '食事詳細を削除しました');
    }

    public function createDetail($nutritionId)
    {
        $nutrition = Nutrition::where('user_id', auth()->id())->findOrFail($nutritionId);
        return view('nutritions.details.create', compact('nutrition'));
    }

    public function storeDetail(Request $request, $nutritionId)
    {
        $nutrition = Nutrition::where('user_id', auth()->id())->findOrFail($nutritionId);

        $detail = new NutritionDetail();
        $detail->nutrition_id = $nutrition->id;
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
