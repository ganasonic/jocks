<?php

namespace App\Http\Controllers;

use App\PracticeVideoUpload;
use App\Services\PracticeVideos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PracticeVideoController extends Controller
{
    public function start(Request $request)
    {
        $data = $request->validate([
            'original_name' => 'required|string|max:255',
            'total_size' => 'required|integer|min:1|max:' . config('practice_video.max_size'),
            'player_id' => 'required|integer|exists:users,id',
        ]);
        PracticeVideos::authorizePlayer($data['player_id']);
        $extension = strtolower(pathinfo($data['original_name'], PATHINFO_EXTENSION));
        if (!in_array($extension, config('practice_video.extensions'), true)) {
            throw ValidationException::withMessages(['original_name' => 'MP4・MOV・M4Vの動画を選択してください。']);
        }
        $token = (string) Str::uuid();
        $upload = PracticeVideoUpload::create([
            'token' => $token,
            'user_id' => auth()->id(),
            'player_id' => $data['player_id'],
            'original_name' => basename($data['original_name']),
            'path' => 'practice-videos/' . $data['player_id'] . '/' . $token . '.' . $extension,
            'total_size' => $data['total_size'],
            'expires_at' => now()->addHours(config('practice_video.expiration_hours')),
        ]);
        return response()->json(['token' => $upload->token, 'chunk_size' => config('practice_video.chunk_size')]);
    }

    private function owned($token)
    {
        $upload = PracticeVideoUpload::where('token', $token)->lockForUpdate()->firstOrFail();
        abort_unless((int) $upload->user_id === (int) auth()->id(), 403);
        PracticeVideos::authorizePlayer($upload->player_id);
        abort_if($upload->expires_at->isPast(), 410, 'アップロードの有効期限が切れました。動画を選び直してください。');
        return $upload;
    }

    public function chunk(Request $request, $token)
    {
        $data = $request->validate(['index' => 'required|integer|min:0', 'chunk' => 'required|file|max:4096']);
        return DB::transaction(function () use ($request, $token, $data) {
            $upload = $this->owned($token);
            abort_unless($upload->status === 'pending', 409);
            $index = (int) $data['index'];
            $size = (int) config('practice_video.chunk_size');
            $expected = min($size, $upload->total_size - $index * $size);
            abort_if($expected <= 0 || $request->file('chunk')->getSize() !== $expected, 422, '分割データのサイズが一致しません。');
            if ($index < (int) $upload->next_chunk) {
                return response()->json(['next_chunk' => (int) $upload->next_chunk]);
            }
            abort_unless($index === (int) $upload->next_chunk, 409, '分割データの順番が一致しません。');
            $disk = Storage::disk('local');
            $disk->makeDirectory(dirname($upload->path));
            $stream = fopen($disk->path($upload->path . '.part'), 'c+b');
            if (!$stream) {
                throw new \RuntimeException('動画の一時ファイルを開けません。');
            }
            try {
                // Write at the committed offset: retry after a DB failure cannot duplicate bytes.
                if (fseek($stream, $index * $size) !== 0 || !ftruncate($stream, $index * $size)) {
                    throw new \RuntimeException('動画の書き込み位置を設定できません。');
                }
                $input = fopen($request->file('chunk')->getRealPath(), 'rb');
                try {
                    if (stream_copy_to_stream($input, $stream) !== $expected) {
                        throw new \RuntimeException('分割データを保存できません。');
                    }
                } finally {
                    fclose($input);
                }
            } finally {
                fclose($stream);
            }
            $upload->update(['next_chunk' => $index + 1, 'expires_at' => now()->addHours(config('practice_video.expiration_hours'))]);
            return response()->json(['next_chunk' => $index + 1]);
        });
    }

    public function complete($token)
    {
        return DB::transaction(function () use ($token) {
            $upload = $this->owned($token);
            if ($upload->status === 'complete') {
                return response()->json(['path' => $upload->path]);
            }
            abort_unless($upload->status === 'pending', 409);
            abort_unless((int) $upload->next_chunk === (int) ceil($upload->total_size / config('practice_video.chunk_size')), 409, '未送信の動画データがあります。');
            $disk = Storage::disk('local');
            // A rename may have succeeded before a previous DB commit failed.
            $source = $disk->exists($upload->path) ? $upload->path : $upload->path . '.part';
            abort_unless($disk->exists($source) && $disk->size($source) === (int) $upload->total_size, 409);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($disk->path($source));
            abort_unless(in_array($mime, config('practice_video.mime_types'), true), 422, '動画の形式を確認できません。MP4・MOV・M4Vを選択してください。');
            if ($source !== $upload->path && !$disk->move($source, $upload->path)) {
                throw new \RuntimeException('動画を保存できません。');
            }
            $upload->update(['status' => 'complete', 'mime_type' => $mime]);
            return response()->json(['path' => $upload->path]);
        });
    }

    public function stream($token)
    {
        $upload = PracticeVideoUpload::where('token', $token)->firstOrFail();
        PracticeVideos::authorizePlayer($upload->player_id);
        if ($upload->status === 'attached') {
            $detail = $upload->detail;
            abort_unless($detail && $detail->practice
                && (int) $detail->practice->user_id === (int) $upload->player_id
                && in_array($upload->path, PracticeVideos::paths($detail->video_url), true), 404);
        } else {
            abort_unless($upload->status === 'complete' && (int) $upload->user_id === (int) auth()->id() && $upload->expires_at->isFuture(), 403);
        }
        abort_unless(Storage::disk('local')->exists($upload->path), 404);
        return response()->file(Storage::disk('local')->path($upload->path), [
            'Content-Type' => $upload->mime_type,
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
