<?php

namespace App\Services;

use App\PracticeDetail;
use App\PracticeVideoUpload;
use Illuminate\Validation\ValidationException;

class PracticeVideos
{
    public static function paths($value)
    {
        if (!$value) {
            return [];
        }
        $paths = is_array($value) ? $value : json_decode($value, true);
        return is_array($paths) ? array_values(array_filter($paths, 'is_string')) : [$value];
    }

    public static function authorizePlayer($playerId)
    {
        abort_unless(auth()->user()->isAssignedPlayer($playerId), 403);
    }

    // Called inside the practice transaction; locks prevent attaching one upload twice.
    public static function attach(PracticeDetail $detail, array $paths, $playerId)
    {
        self::authorizePlayer($playerId);
        $previous = self::paths($detail->video_url);
        foreach ($paths as $path) {
            $upload = PracticeVideoUpload::where('path', $path)->lockForUpdate()->first();
            if (!$upload) {
                // Legacy external URLs may be retained only on their original detail.
                if (in_array($path, $previous, true) && preg_match('~^https?://~i', $path)) {
                    continue;
                }
                throw ValidationException::withMessages(['details' => '動画のアップロード情報が見つかりません。']);
            }
            $existing = $upload->status === 'attached' && (int) $upload->detail_id === (int) $detail->id;
            $pending = $upload->status === 'complete'
                && (int) $upload->user_id === (int) auth()->id()
                && $upload->expires_at->isFuture();
            if ((!$existing && !$pending) || (int) $upload->player_id !== (int) $playerId) {
                throw ValidationException::withMessages(['details' => 'この動画を登録する権限がないか、有効期限が切れています。']);
            }
            $upload->update(['detail_id' => $detail->id, 'status' => 'attached']);
        }
        // Removal is transactional; the cleanup command deletes the files later.
        PracticeVideoUpload::where('detail_id', $detail->id)->whereNotIn('path', $paths)
            ->update(['detail_id' => null, 'status' => 'removed', 'expires_at' => now()]);
        $detail->update(['video_url' => $paths ? json_encode(array_values(array_unique($paths)), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null]);
    }
}
