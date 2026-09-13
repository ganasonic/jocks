@foreach($paths as $path)
    <div class="mt-2">
        @if(preg_match('~^practice-videos/\d+/([a-f0-9-]{36})\.(mp4|mov|m4v)$~', $path, $matches))
            <video controls playsinline preload="none" class="w-100 rounded" style="max-height:360px" src="{{ route('practice-videos.stream', $matches[1]) }}"></video>
            <a href="{{ route('practice-videos.stream', $matches[1]) }}" target="_blank" rel="noopener">動画を開く</a>
        @elseif(preg_match('~^https?://~i', $path))
            <a href="{{ $path }}" target="_blank" rel="noopener noreferrer">登録済みの動画を開く</a>
        @endif
    </div>
@endforeach
