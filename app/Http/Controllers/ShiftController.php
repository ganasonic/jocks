<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shift;
use Auth;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //マイシフト
    public function myshift()
    {
        $title = "マイシフト";
        $message = 'ロードされました。';
        $user = Auth::user();
        //dd($user);
        //
        date_default_timezone_set('Asia/Tokyo'); // 日本時間に設定
        $today = date('Y-m-d');
        // 前日の日付を取得
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        // 翌日の日付を取得
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // 既存のシフトを取得
        $shift = Shift::where('user_id', $user->id)->where('shift_date', $today)->first();
        $shift_pre = Shift::where('user_id', $user->id)->where('shift_date', $yesterday)->first();
        $shift_pst = Shift::where('user_id', $user->id)->where('shift_date', $tomorrow)->first();
        //dd($shift);
        if(!$shift){
            $shift = (object)[
                'shift_date' => $today,
                'breakfast' => '',
                'am_shift' => '',
                'lunch' => '',
                'pm_shift' => '',
                'dinner' => '',
                'stay' => '',
                'bus_outward' => '',
                'bus_return' => '',
                'property' => '',
                'comment' => ''
            ];
        }
        if(!$shift_pre){
            $shift_pre = (object)[
                'shift_date' => $yesterday,
                'breakfast' => '',
                'am_shift' => '',
                'lunch' => '',
                'pm_shift' => '',
                'dinner' => '',
                'stay' => '',
                'bus_outward' => '',
                'bus_return' => '',
                'property' => '',
                'comment' => ''
            ];
        }
        if(!$shift_pst){
            $shift_pst = (object)[
                'shift_date' => $tomorrow,
                'breakfast' => '',
                'am_shift' => '',
                'lunch' => '',
                'pm_shift' => '',
                'dinner' => '',
                'stay' => '',
                'bus_outward' => '',
                'bus_return' => '',
                'property' => '',
                'comment' => ''
            ];
        }
        $shift->weekday = $this->getWeek($today);
        $shift_pre->weekday = $this->getWeek($yesterday);
        $shift_pst->weekday = $this->getWeek($tomorrow);
        return view('myshift', compact(
            'title',
            'user',
            'shift',
            'shift_pre',
            'shift_pst',
            'message'
        ));
    }

    //日付指定マイシフト
    public function datechange($date)
    {
        $title = "マイシフト";
        $message = '日付が変わりました。';
        $user = Auth::user();

        // 前日の日付を取得
        $yesterday = date('Y-m-d', strtotime($date . ' -1 day'));
        // 翌日の日付を取得
        $tomorrow = date('Y-m-d', strtotime($date . ' +1 day'));

        // 既存のシフトを取得
        $shift = Shift::where('user_id', $user->id)->where('shift_date', $date)->first();
        //dd($shift);
        $shift_pre = Shift::where('user_id', $user->id)->where('shift_date', $yesterday)->first();
        $shift_pst = Shift::where('user_id', $user->id)->where('shift_date', $tomorrow)->first();

        //dd($shift);
        if(!$shift){
            $shift = (object)[
                'shift_date' => $date,
                'breakfast' => '',
                'am_shift' => '',
                'lunch' => '',
                'pm_shift' => '',
                'dinner' => '',
                'stay' => '',
                'bus_outward' => '',
                'bus_return' => '',
                'property' => '',
                'comment' => ''
            ];
        }
        if(!$shift_pre){
            $shift_pre = (object)[
                'shift_date' => $yesterday,
                'breakfast' => '',
                'am_shift' => '',
                'lunch' => '',
                'pm_shift' => '',
                'dinner' => '',
                'stay' => '',
                'bus_outward' => '',
                'bus_return' => '',
                'property' => '',
                'comment' => ''
            ];
        }
        if(!$shift_pst){
            $shift_pst = (object)[
                'shift_date' => $tomorrow,
                'breakfast' => '',
                'am_shift' => '',
                'lunch' => '',
                'pm_shift' => '',
                'dinner' => '',
                'stay' => '',
                'bus_outward' => '',
                'bus_return' => '',
                'property' => '',
                'comment' => ''
            ];
        }
        $shift->weekday = $this->getWeek($date);
        $shift_pre->weekday = $this->getWeek($yesterday);
        $shift_pst->weekday = $this->getWeek($tomorrow);
        return view('myshift', compact(
            'title',
            'user',
            'shift',
            'shift_pre',
            'shift_pst',
            'message'
        ));
    }

    //シフト編集更新
    public function shiftupdate(Request $request, $id)
    {
        $title = "マイシフト";
        $message = '更新しました。';
        $user = Auth::user();
        $shift_date = $request->input('specifieddate'); // 日付

        // 既存のシフトを取得
        $shift = Shift::where('user_id', $id)->where('shift_date', $shift_date)->first();
        // シフトが存在しない場合は新規作成
        if($shift==null){
            $shift = new Shift;
            //dd('shifts null');
        }
        //dd($request);
        $shift->shift_date = $shift_date; // 日付
        $shift->user_id = $id; // USERID
        $shift->breakfast = $request->input('tdy_breakfast') ? true : false; // 朝食
        $shift->am_shift = $request->input('tdy_am_shift') ? true : false; // 午前
        $shift->lunch = $request->input('tdy_lunch') ? true : false; // 昼食
        $shift->pm_shift = $request->input('tdy_pm_shift') ? true : false; // 午後
        $shift->dinner = $request->input('tdy_dinner') ? true : false; // 夕食
        $shift->stay = $request->input('tdy_stay') ? true : false; // 宿泊
        $shift->bus_outward = $request->input('tdy_bus_outward') ? true : false; // バス往路
        $shift->bus_return = $request->input('tdy_bus_return') ? true : false; // バス復路
        $shift->property = null;    // 属性
        $shift->comment = $request->input('tdy_comment');  // コメント
        $shift->flag = 0;// 削除フラグ

        //dd($shift);
        $shift->save();

        // 前日の日付を取得
        $yesterday = date('Y-m-d', strtotime($shift_date . ' -1 day'));
        // 翌日の日付を取得
        $tomorrow = date('Y-m-d', strtotime($shift_date . ' +1 day'));

        $shift_pre = Shift::where('user_id', $user->id)->where('shift_date', $yesterday)->first();
        $shift_pst = Shift::where('user_id', $user->id)->where('shift_date', $tomorrow)->first();

        if(!$shift_pre){
            $shift_pre = (object)[
                'shift_date' => $yesterday,
                'breakfast' => '',
                'am_shift' => '',
                'lunch' => '',
                'pm_shift' => '',
                'dinner' => '',
                'stay' => '',
                'bus_outward' => '',
                'bus_return' => '',
                'property' => '',
                'comment' => ''
            ];
        }
        if(!$shift_pst){
            $shift_pst = (object)[
                'shift_date' => $tomorrow,
                'breakfast' => '',
                'am_shift' => '',
                'lunch' => '',
                'pm_shift' => '',
                'dinner' => '',
                'stay' => '',
                'bus_outward' => '',
                'bus_return' => '',
                'property' => '',
                'comment' => ''
            ];
        }
        $shift->weekday = $this->getWeek($shift_date);
        $shift_pre->weekday = $this->getWeek($yesterday);
        $shift_pst->weekday = $this->getWeek($tomorrow);
        //dd($shift_pre,$shift,$shift_pst,);
        return view('myshift', compact(
            'title',
            'user',
            'shift',
            'shift_pre',
            'shift_pst',
            'message'
        ));

        //
        //$pre_breakfast =>$request->input('pre_breakfast');
        //$pre_am_shift =>$request->input('pre_am_shift');
        //$pre_lunch => $request->input('pre_lunch');
        //$pre_pm_shift => $request->input('pre_pm_shift');
        //$pre_dinner => $request->input('pre_dinner');
        //$pre_stay => $request->input('pre_stay');
        //$pre_bus_outward =>$request->input('pre_bus_outward');
        //$pre_bus_return => $request->input('pre_bus_return');
        //$pre_comment => $request->input('pre_comment');
//
        //$tdy_am_shift =>$request->input('tdy_breakfast');
        //$tdy_am_shift =>$request->input('tdy_am_shift');
        //$tdy_lunch => $request->input('tdy_lunch');
        //$tdy_pm_shift => $request->input('tdy_pm_shift');
        //$tdy_dinner => $request->input('tdy_dinner');
        //$tdy_stay => $request->input('tdy_stay');
        //$tdy_bus_outward =>$request->input('tdy_bus_outward');
        //$tdy_bus_return => $request->input('tdy_bus_return');
        //$tdy_comment => $request->input('tdy_comment');
//
        //$pst_breakfast =>$request->input('pst_breakfast');
        //$pst_am_shift =>$request->input('pst_am_shift');
        //$pst_lunch => $request->input('pst_lunch');
        //$pst_pm_shift => $request->input('pst_pm_shift');
        //$pst_dinner => $request->input('pst_dinner');
        //$pst_stay => $request->input('pst_stay');
        //$pst_bus_outward =>$request->input('pst_bus_outward');
        //$pst_bus_return => $request->input('pst_bus_return');
        //$pst_comment => $request->input('pst_comment');
    }

    public function getWeek($date)
    {
        // 曜日を漢字に変換
        $weekday_map = [
            'Sunday' => '(日)',
            'Monday' => '(月)',
            'Tuesday' => '(火)',
            'Wednesday' => '(水)',
            'Thursday' => '(木)',
            'Friday' => '(金)',
            'Saturday' => '(土)'
        ];
        $day_weekday = date('l', strtotime($date));
        return $weekday_map[$day_weekday];
    }

    //当日詳細
    public function detail($shift_date=null)
    {
        //
        $title = "当日詳細";
        $user = Auth::user();
        //dd($user);
        //
        date_default_timezone_set('Asia/Tokyo'); // 日本時間に設定
        $today = date('Y-m-d');
        $shift_date = $today;

        // 既存のシフトを取得
        $shifts = Shift::where('shift_date', $today)->with('user')->get();
        //dd($shifts);
        if(!$shifts){
            $title = "エラー";
            $errortext = "エラーが発生しました。";
            $errorreason = "理由は知らんけど。";
            return view('detail', compact('title','user', 'errortext', 'errorreason'));
            }
        $weekday = $this->getWeek($today);
        $totals = [
            'breakfast' => $shifts->where('breakfast', 1)->count(),
            'am_shift' => $shifts->where('am_shift', 1)->count(),
            'lunch' => $shifts->where('lunch', 1)->count(),
            'pm_shift' => $shifts->where('pm_shift', 1)->count(),
            'dinner' => $shifts->where('dinner', 1)->count(),
            'stay' => $shifts->where('stay', 1)->count(),
            'bus_outward' => $shifts->where('bus_outward', 1)->count(),
            'bus_return' => $shifts->where('bus_return', 1)->count(),
        ];
        $names = $this->getEachName($shifts);
        //dd($names['breakfast']);

        return view('detail', compact(
            'title',
            'user',
            'totals',
            'shifts',
            'shift_date',
            'weekday',
            'names'
        ));
    }

    //指定日詳細
    public function specified($shift_date)
    {
        //
        //$this->detail($shift_date);
        $title = "指定日詳細";
        $user = Auth::user();

        // 既存のシフトを取得
        $shifts = Shift::where('shift_date', $shift_date)
            ->with('user')
            ->orderBy('user_id', 'asc')
            ->get();
        //dd($shifts);
        if(!$shifts){
            $title = "エラー";
            $errortext = "エラーが発生しました。";
            $errorreason = "理由は知らんけど。";
            return view('detail', compact('title','user', 'errortext', 'errorreason'));
            }
        $weekday = $this->getWeek($shift_date);

        $totals = [
            'breakfast' => $shifts->where('breakfast', 1)->count(),
            'am_shift' => $shifts->where('am_shift', 1)->count(),
            'lunch' => $shifts->where('lunch', 1)->count(),
            'pm_shift' => $shifts->where('pm_shift', 1)->count(),
            'dinner' => $shifts->where('dinner', 1)->count(),
            'stay' => $shifts->where('stay', 1)->count(),
            'bus_outward' => $shifts->where('bus_outward', 1)->count(),
            'bus_return' => $shifts->where('bus_return', 1)->count(),
        ];
        $names = $this->getEachName($shifts);
        //dd($names);

        return view('detail', compact(
            'title',
            'user',
            'totals',
            'shifts',
            'shift_date',
            'weekday',
            'names'
        ));
    }

    public function getEachName($shifts){
        // breakfast が 1 のユーザーの名前を取得
        $breakfast_names = $shifts->filter(function ($shift) {
            return $shift->breakfast == 1; // breakfast が 1 の条件
        })->map(function ($shift) {
            return $shift->user->name; // 名前を取得
        });
        // am_shift が 1 のユーザーの名前を取得
        $am_shift_names = $shifts->filter(function ($shift) {
            return $shift->am_shift == 1; // am_shift が 1 の条件
        })->map(function ($shift) {
            return $shift->user->name; // 名前を取得
        });
        // lunch が 1 のユーザーの名前を取得
        $lunch_names = $shifts->filter(function ($shift) {
            return $shift->lunch == 1; // lunch が 1 の条件
        })->map(function ($shift) {
            return $shift->user->name; // 名前を取得
        });
        // pm_shift が 1 のユーザーの名前を取得
        $pm_shift_names = $shifts->filter(function ($shift) {
            return $shift->pm_shift == 1; // pm_shift が 1 の条件
        })->map(function ($shift) {
            return $shift->user->name; // 名前を取得
        });
        // dinner が 1 のユーザーの名前を取得
        $dinner_names = $shifts->filter(function ($shift) {
            return $shift->dinner == 1; // dinner が 1 の条件
        })->map(function ($shift) {
            return $shift->user->name; // 名前を取得
        });
        // stay が 1 のユーザーの名前を取得
        $stay_names = $shifts->filter(function ($shift) {
            return $shift->stay == 1; // stay が 1 の条件
        })->map(function ($shift) {
            return $shift->user->name; // 名前を取得
        });
        // bus_outward が 1 のユーザーの名前を取得
        $bus_outward_names = $shifts->filter(function ($shift) {
            return $shift->bus_outward == 1; // bus_outward が 1 の条件
        })->map(function ($shift) {
            return $shift->user->name; // 名前を取得
        });
        // bus_return が 1 のユーザーの名前を取得
        $bus_return_names = $shifts->filter(function ($shift) {
            return $shift->bus_return == 1; // bus_return が 1 の条件
        })->map(function ($shift) {
            return $shift->user->name; // 名前を取得
        });

        $names = [
            'breakfast' => $breakfast_names,
            'am_shift' => $am_shift_names,
            'lunch' => $lunch_names,
            'pm_shift' => $pm_shift_names,
            'dinner' => $dinner_names,
            'stay' => $stay_names,
            'bus_outward' => $bus_outward_names,
            'bus_return' => $bus_return_names,
        ];

        $names = [
            'breakfast' => $breakfast_names->toArray(),
            'am_shift' => $am_shift_names->toArray(),
            'lunch' => $lunch_names->toArray(),
            'pm_shift' => $pm_shift_names->toArray(),
            'dinner' => $dinner_names->toArray(),
            'stay' => $stay_names->toArray(),
            'bus_outward' => $bus_outward_names->toArray(),
            'bus_return' => $bus_return_names->toArray(),
        ];
        return $names;
    }

    //食事集計
    public function meal()
    {
        //
        $title = "食事集計";
        $errortext = "";
        $errorreason = "";

        $user = Auth::user();
        //
        date_default_timezone_set('Asia/Tokyo'); // 日本時間に設定
        $today = date('Y-m-d');
        $shift_date = $today;

        // 翌日の日付を取得
        $shift_date1 = date('Y-m-d', strtotime('+1 day'));
        // 翌翌日の日付を取得
        $shift_date2 = date('Y-m-d', strtotime('+2 day'));

        // 既存のシフトを取得
        $shifts = Shift::where('shift_date', $shift_date)->with('user')->get();
        $shifts1 = Shift::where('shift_date', $shift_date1)->with('user')->get();
        $shifts2 = Shift::where('shift_date', $shift_date2)->with('user')->get();
        if(!$shifts){
            $errortext .= "エラーが発生しました。";
            $errorreason .= "理由は知らんけど。";
        }
        if(!$shifts1){
            $errortext .= "エラーが発生しました。";
            $errorreason .= "理由は知らんけど。";
        }
        if(!$shifts2){
            $errortext .= "エラーが発生しました。";
            $errorreason .= "理由は知らんけど。";
        }
        if(strlen($errortext)>0){
            return view('detail', compact('title','user', 'errortext', 'errorreason'));
        }
        $weekday = $this->getWeek($shift_date);
        $weekday1 = $this->getWeek($shift_date1);
        $weekday2 = $this->getWeek($shift_date2);
        $totals = [
            'breakfast' => $shifts->where('breakfast', 1)->count(),
            'lunch' => $shifts->where('lunch', 1)->count(),
            'dinner' => $shifts->where('dinner', 1)->count(),
        ];
        $totals1 = [
            'breakfast' => $shifts1->where('breakfast', 1)->count(),
            'lunch' => $shifts1->where('lunch', 1)->count(),
            'dinner' => $shifts1->where('dinner', 1)->count(),
        ];
        $totals2 = [
            'breakfast' => $shifts2->where('breakfast', 1)->count(),
            'lunch' => $shifts2->where('lunch', 1)->count(),
            'dinner' => $shifts2->where('dinner', 1)->count(),
        ];
        //dd($totals, $totals1, $totals2);
        return view('meal', compact(
            'title',
            'shift_date',
            'shift_date1',
            'shift_date2',
            'totals',
            'totals1',
            'totals2',
            'weekday',
            'weekday1',
            'weekday2'
        ));
    }

    //月間シフト
    public function monthly()
    {
        //
        $title = "月間シフト";
        return view('monthly', compact('title'));
    }

    //集計
    public function totalling()
    {
        //
        $title = "集計";
        return view('totalling', compact('title'));
    }

    //従食確認
    public function lunch()
    {
        //
        $title = "従食確認";
        return view('lunch', compact('title'));
    }

    //寮食確認
    public function dormitorymeal()
    {
        //
        $title = "寮食確認";
        return view('dormitorymeal', compact('title'));
    }

    //宿泊確認
    public function dormitorystay()
    {
        //
        $title = "宿泊確認";
        //return view('dormitorystay', compact('title'));

        $errortext = "";
        $errorreason = "";

        $user = Auth::user();
        //
        date_default_timezone_set('Asia/Tokyo'); // 日本時間に設定
        $today = date('Y-m-d');
        $shift_date = $today;

        // 翌日の日付を取得
        $shift_date1 = date('Y-m-d', strtotime('+1 day'));
        // 翌翌日の日付を取得
        $shift_date2 = date('Y-m-d', strtotime('+2 day'));

        // 既存のシフトを取得
        $shifts = Shift::where('shift_date', $shift_date)->with('user')->get();
        $shifts1 = Shift::where('shift_date', $shift_date1)->with('user')->get();
        $shifts2 = Shift::where('shift_date', $shift_date2)->with('user')->get();
        if(!$shifts){
            $errortext .= "エラーが発生しました。";
            $errorreason .= "理由は知らんけど。";
        }
        if(!$shifts1){
            $errortext .= "エラーが発生しました。";
            $errorreason .= "理由は知らんけど。";
        }
        if(!$shifts2){
            $errortext .= "エラーが発生しました。";
            $errorreason .= "理由は知らんけど。";
        }
        if(strlen($errortext)>0){
            return view('detail', compact('title','user', 'errortext', 'errorreason'));
        }
        $weekday = $this->getWeek($shift_date);
        $weekday1 = $this->getWeek($shift_date1);
        $weekday2 = $this->getWeek($shift_date2);
        $totals = [
            'stay' => $shifts->where('stay', 1)->count(),
        ];
        $totals1 = [
            'stay' => $shifts1->where('stay', 1)->count(),
        ];
        $totals2 = [
            'stay' => $shifts2->where('stay', 1)->count(),
        ];
        //dd($totals, $totals1, $totals2);
        return view('dormitorystay', compact(
            'title',
            'shift_date',
            'shift_date1',
            'shift_date2',
            'totals',
            'totals1',
            'totals2',
            'weekday',
            'weekday1',
            'weekday2'
        ));


    }

    //送迎確認
    public function pickupbus()
    {
        //
        $title = "送迎確認";
        return view('pickupbus', compact('title'));
    }



}
