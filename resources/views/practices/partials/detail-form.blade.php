<div class="card shadow-sm border-light-subtle mb-3 detail-item">
    <div class="card-body p-3">
        @if(!empty($row['id']))
            <input type="hidden" name="details[{{ $index }}][id]" value="{{ $row['id'] }}">
        @endif
        <div class="row g-2 mb-2">
            <div class="col-md-4">
                <label class="form-label fw-bold">メニュー名 <span class="text-danger">*</span></label>
                <input type="text" name="details[{{ $index }}][menu_name]" class="form-control form-control-sm" value="{{ $row['menu_name'] ?? '' }}" list="practice-menu-options" required autocomplete="off">
            </div>
            <div class="col-md-2">
                <label class="form-label">本数/時間</label>
                <select name="details[{{ $index }}][runs_or_time]" class="form-select form-select-sm">
                    <option value="">選択</option>
                    @foreach(['1本', '3本', '5本', '10本', '15本', '20本', '5分', '10分', '15分', '20分', '30分', '60分'] as $value)
                        <option value="{{ $value }}" {{ ($row['runs_or_time'] ?? '') == $value ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            @foreach(['rating' => '本人達成度', 'coach_rating' => 'コーチ達成度'] as $field => $label)
                <div class="col-md-3">
                    <label class="form-label">{{ $label }}</label>
                    @if($field === 'rating' || auth()->user()->isStaff())
                        <select name="details[{{ $index }}][{{ $field }}]" class="form-select form-select-sm">
                            <option value="">選択なし</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ ($row[$field] ?? '') == $i ? 'selected' : '' }}>{{ $i }} ({{ str_repeat('★', $i) }})</option>
                            @endfor
                        </select>
                    @else
                        <div>{{ $row[$field] ?? '-' }}</div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="row g-2 mb-2">
            @foreach(['impression' => '感触・自己評価', 'notice' => '課題・反省'] as $field => $label)
                <div class="col-md-6"><label class="form-label">{{ $label }}</label><textarea name="details[{{ $index }}][{{ $field }}]" class="form-control form-control-sm" rows="2">{{ $row[$field] ?? '' }}</textarea></div>
            @endforeach
        </div>
        <div class="mb-3">
            <label class="form-label text-success fw-bold">アドバイス（コーチコメント）</label>
            @if(auth()->user()->isStaff())
                <textarea name="details[{{ $index }}][feedback]" class="form-control form-control-sm border-success-subtle bg-success-subtle" rows="2">{{ $row['feedback'] ?? '' }}</textarea>
            @else
                <div class="form-control bg-light">{{ $row['feedback'] ?? 'フィードバックはありません。' }}</div>
            @endif
        </div>
        <div class="practice-video-upload" data-index="{{ $index }}">
            <label class="form-label fw-bold">動画（複数選択可）</label>
            <input type="file" class="form-control video-files" accept=".mp4,.mov,.m4v,video/mp4,video/quicktime,video/x-m4v" multiple>
            <div class="form-text">MP4・MOV・M4V、1本{{ config('practice_video.max_size') / 1024 / 1024 }}MBまで、最大{{ config('practice_video.max_files') }}本。追加後に練習記録を保存してください。</div>
            <div class="video-status small mt-2" role="status" aria-live="polite"></div>
            <div class="video-list row g-3 mt-1">
                @foreach(\App\Services\PracticeVideos::paths($row['video_url'] ?? null) as $path)
                    <div class="col-md-6 video-item">
                        <input type="hidden" name="details[{{ $index }}][video_url][]" value="{{ $path }}">
                        @include('practices.partials.videos', ['paths' => [$path]])
                        <button type="button" class="btn btn-sm btn-outline-danger remove-video mt-1">動画を外す</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
