<datalist id="practice-menu-options">
    @foreach(array_unique(array_merge(['フラット', 'ライン取り', 'ポジション', 'フラット・カービング', 'スピード', 'コーク720', 'フルツイスト', 'グラブ練習'], isset($recentMenus) ? $recentMenus->all() : [])) as $menu)
        <option value="{{ $menu }}">
    @endforeach
</datalist>
<script>
window.practiceVideoConfig = {
    startUrl: @json(route('practice-videos.start')),
    playerId: @json(auth()->user()->targetPlayerId()),
    maxSize: @json(config('practice_video.max_size')),
    maxFiles: @json(config('practice_video.max_files'))
};
</script>
<script src="{{ asset('js/practice-videos.js') }}" defer></script>
