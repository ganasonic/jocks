@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white py-3 fw-bold">
                    🔄 PDCAサイクル：振り返りとコーチフィードバック
                </div>

                <div class="card-body p-4">
                    {{-- 設定した目標の確認エリア --}}
                    <div class="alert alert-info py-3 mb-4">
                        <span class="badge bg-primary mb-2">設定時の内容</span>
                        <h4 class="fw-bold text-dark">{{ $goal->title }}</h4>
                        <p class="mb-0 small text-muted">期間: {{ $goal->start_date }} 〜 {{ $goal->end_date }}</p>
                        @if($goal->action_plan)
                            <hr class="my-2">
                            <p class="mb-0 small"><strong>行動プラン:</strong><br>{!! nl2br(e($goal->action_plan)) !!}</p>
                        @endif
                    </div>

                    <form action="{{ route('goals.update', $goal->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        {{-- 目標自体の微調整も可能に --}}
                        <!--
                        <input type="hidden" name="title" value="{{ $goal->title }}">
                        <input type="hidden" name="action_plan" value="{{ $goal->action_plan }}">
                        -->
                        <h5 class="fw-bold text-danger border-bottom pb-2 mb-3">🎯 目標設定の編集（Plan）</h5>

                        {{-- 期間の種類（変更不可にする場合はそのまま、今回はセレクトボックスに） --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">期間の種類</label>
                            <select name="period_type" class="form-control" required>
                                <option value="daily" {{ old('period_type', $goal->period_type) == 'daily' ? 'selected' : '' }}>日単位</option>
                                <option value="weekly" {{ old('period_type', $goal->period_type) == 'weekly' ? 'selected' : '' }}>週単位</option>
                                <option value="monthly" {{ old('period_type', $goal->period_type) == 'monthly' ? 'selected' : '' }}>月単位</option>
                                <option value="yearly" {{ old('period_type', $goal->period_type) == 'yearly' ? 'selected' : '' }}>年単位</option>
                            </select>
                        </div>

                        {{-- 開始日・終了日 --}}
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold">開始日</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $goal->start_date) }}" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold">終了日</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $goal->end_date) }}" required>
                            </div>
                        </div>

                        {{-- 目標タイトル --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">目指す目標（成果目標）</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $goal->title) }}" required>
                        </div>

                        {{-- アクションプラン --}}
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">行動プラン（具体的なアクション）</label>
                            <textarea name="action_plan" class="form-control" rows="3">{{ old('action_plan', $goal->action_plan) }}</textarea>
                        </div>

                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">📝 自分の振り返り（Check & Action）</h5>

                        {{-- 達成率 --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">自己評価・達成度 (0〜100%)</label>
                            <div class="input-group">
                                <input type="number" name="achievement_rate" class="form-control" min="0" max="100" value="{{ old('achievement_rate', $goal->achievement_rate) }}" placeholder="85">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        {{-- 結果 --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">結果（Do）</label>
                            <textarea name="result_memo" class="form-control" rows="3" placeholder="実際の成果や行動できた実績を書きます。">{{ old('result_memo', $goal->result_memo) }}</textarea>
                        </div>

                        {{-- 感想 --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">感想</label>
                            <textarea name="impression" class="form-control" rows="2" placeholder="やってみてどう感じたか、キツかったことなど。">{{ old('impression', $goal->impression) }}</textarea>
                        </div>

                        {{-- 原因と対策 --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">未達の原因、または更なる改善への対策（Analysis）</label>
                            <textarea name="countermeasure" class="form-control" rows="3" placeholder="なぜ達成できなかったか、次への改善策。">{{ old('countermeasure', $goal->countermeasure) }}</textarea>
                        </div>

                        {{-- 次への引き継ぎ --}}
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">次への引き継ぎ事項（Next Action）</label>
                            <textarea name="next_action" class="form-control" rows="2" placeholder="次の期間の目標にどう活かすか。">{{ old('next_action', $goal->next_action) }}</textarea>
                        </div>


                        <h5 class="fw-bold text-success border-bottom pb-2 mb-3">👨‍🏫 コーチのフィードバック（Coach）</h5>

                        {{-- コーチコメント欄 --}}
                        <div class="form-group mb-4 bg-light p-3 rounded border border-success">
                            <label class="form-label fw-bold text-success">コーチのコメント入力欄</label>
                            <textarea name="coach_comment" class="form-control bg-white" rows="4" placeholder="（指導者・コーチが選手の振り返りを見てアドバイスを書き込みます）">{{ old('coach_comment', $goal->coach_comment) }}</textarea>
                        </div>


                        <div class="d-flex gap-2">
                            <a href="{{ route('goals.index', ['period_type' => $goal->period_type]) }}" class="btn btn-outline-secondary w-50">キャンセル</a>
                            <button type="submit" class="btn btn-success w-50">振り返り・コメントを保存</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
