<?php

namespace App\Console\Commands;

use App\PracticeVideoUpload;
use App\Services\PracticeVideos;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanupPracticeVideos extends Command
{
    protected $signature = 'practice-videos:cleanup';
    protected $description = '期限切れの未保存動画と、練習から削除された動画を削除します';

    public function handle()
    {
        PracticeVideoUpload::where(function ($query) {
            $query->where('expires_at', '<=', now())->orWhere('status', 'attached');
        })->chunkById(100, function ($uploads) {
            foreach ($uploads as $candidate) {
                DB::transaction(function () use ($candidate) {
                    $upload = PracticeVideoUpload::where('id', $candidate->id)->lockForUpdate()->first();
                    if (!$upload) {
                        return;
                    }
                    if ($upload->status === 'attached') {
                        $detail = $upload->detail;
                        if ($detail && $detail->practice && in_array($upload->path, PracticeVideos::paths($detail->video_url), true)) {
                            return;
                        }
                    } elseif ($upload->expires_at->isFuture()) {
                        return;
                    }
                    foreach ([$upload->path, $upload->path . '.part'] as $path) {
                        if (Storage::disk('local')->exists($path) && !Storage::disk('local')->delete($path)) {
                            throw new \RuntimeException('動画を削除できませんでした。');
                        }
                    }
                    $upload->delete();
                });
            }
        });
        $this->info('不要な練習動画を削除しました。');
    }
}
