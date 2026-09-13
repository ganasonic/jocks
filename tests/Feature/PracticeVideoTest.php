<?php

namespace Tests\Feature;

use App\Practice;
use App\PracticeDetail;
use App\PracticeVideoUpload;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PracticeVideoTest extends TestCase
{
    protected $player;

    protected function setUp(): void
    {
        parent::setUp();
        // Only an isolated in-memory database is allowed for these tests.
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        require_once database_path('migrations/2014_10_12_000000_create_users_table.php');
        (new \CreateUsersTable)->up();
        Schema::table('users', function (Blueprint $table) { $table->integer('role')->default(1); });
        require_once database_path('migrations/2026_07_20_032725_create_practices_table.php');
        (new \CreatePracticesTable)->up();
        Schema::table('practices', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->text('feedback')->nullable();
        });
        require_once database_path('migrations/2026_07_20_032728_create_practice_details_table.php');
        (new \CreatePracticeDetailsTable)->up();
        Schema::table('practice_details', function (Blueprint $table) { $table->text('feedback')->nullable(); });
        require_once database_path('migrations/2026_08_11_131926_create_player_staff_table.php');
        (new \CreatePlayerStaffTable)->up();
        require_once database_path('migrations/2026_09_13_000001_add_practice_video_uploads.php');
        (new \AddPracticeVideoUploads)->up();
        Storage::fake('local');
        $this->player = $this->user('player', 1);
        $this->actingAs($this->player);
    }

    private function user($name, $role)
    {
        return User::create(['name' => $name, 'email' => $name . '@example.test', 'password' => bcrypt('password'), 'role' => $role]);
    }

    private function start($size = 32)
    {
        return $this->postJson('/practice-videos', ['original_name' => '練習.mp4', 'total_size' => $size, 'player_id' => $this->player->id]);
    }

    private function file($bytes)
    {
        $path = tempnam(sys_get_temp_dir(), 'practice-video-test-');
        file_put_contents($path, $bytes);
        return new UploadedFile($path, 'chunk.bin', 'application/octet-stream', null, true);
    }

    private function chunk($token, $index, $bytes)
    {
        $file = $this->file($bytes);
        try {
            return $this->postJson('/practice-videos/' . $token . '/chunks', ['index' => $index, 'chunk' => $file]);
        } finally {
            @unlink($file->getRealPath());
        }
    }

    private function videoBytes($size = 32)
    {
        return pack('N', 32) . 'ftypisom' . pack('N', 512) . 'isomiso2avc1mp41' . str_repeat("\0", $size - 32);
    }

    private function completed()
    {
        $token = $this->start()->assertOk()->json('token');
        $this->chunk($token, 0, $this->videoBytes())->assertOk();
        $path = $this->postJson('/practice-videos/' . $token . '/complete')->assertOk()->json('path');
        return [$token, $path];
    }

    private function payload($paths)
    {
        return ['player_id' => $this->player->id, 'practice_date' => '2026-09-13', 'details' => [
            ['menu_name' => 'フラット', 'video_url' => $paths],
        ]];
    }

    public function test_chunks_retry_without_duplicate_bytes_and_complete_idempotently()
    {
        $size = 4 * 1024 * 1024;
        $token = $this->start($size + 32)->assertOk()->json('token');
        $this->chunk($token, 1, str_repeat("\0", 32))->assertStatus(409);
        $this->chunk($token, 0, 'short')->assertStatus(422);
        $this->chunk($token, 0, $this->videoBytes($size))->assertOk();
        $this->chunk($token, 0, $this->videoBytes($size))->assertOk()->assertJson(['next_chunk' => 1]);
        $this->postJson('/practice-videos/' . $token . '/complete')->assertStatus(409);
        $this->chunk($token, 1, str_repeat("\0", 32))->assertOk();
        $result = $this->postJson('/practice-videos/' . $token . '/complete')->assertOk();
        $this->postJson('/practice-videos/' . $token . '/complete')->assertOk()->assertJson($result->json());
        $this->assertSame($size + 32, Storage::disk('local')->size($result->json('path')));
        $this->get('/practice-videos/' . $token, ['Range' => 'bytes=0-31'])->assertStatus(206);
    }

    public function test_multiple_videos_save_render_and_survive_editing()
    {
        list($token, $path) = $this->completed();
        list($otherToken, $otherPath) = $this->completed();
        $this->post('/practices', $this->payload([$path, $otherPath]))->assertRedirect(route('practices.index'));
        $practice = Practice::first();
        $detail = $practice->details()->first();
        $this->assertSame([$path, $otherPath], json_decode($detail->video_url, true));
        $this->assertSame('attached', PracticeVideoUpload::where('token', $token)->first()->status);
        $this->get('/practices/' . $practice->id)->assertOk()->assertSee('/practice-videos/' . $token);
        $this->get('/practices/' . $practice->id . '/edit')->assertOk()->assertSee('detail-template');
        $this->get('/practices')->assertOk()->assertSee('/practice-videos/' . $token);
        $payload = $this->payload([$path]);
        $payload['details'][0]['id'] = $detail->id;
        $this->patch('/practices/' . $practice->id, $payload)->assertRedirect();
        $this->assertSame([$path], json_decode($detail->fresh()->video_url, true));
        $this->get('/practice-videos/' . $otherToken)->assertStatus(403);
        $this->artisan('practice-videos:cleanup')->assertExitCode(0);
        Storage::disk('local')->assertMissing($otherPath);
        Storage::disk('local')->assertExists($path);
    }

    public function test_guest_and_other_users_cannot_upload_attach_or_stream()
    {
        list($token, $path) = $this->completed();
        $other = $this->user('other', 1);
        $this->actingAs($other);
        $this->start()->assertStatus(403);
        $this->chunk($token, 0, $this->videoBytes())->assertStatus(403);
        $this->postJson('/practice-videos/' . $token . '/complete')->assertStatus(403);
        $this->get('/practice-videos/' . $token)->assertStatus(403);
        $payload = $this->payload([$path]);
        $payload['player_id'] = $other->id;
        $this->postJson('/practices', $payload)->assertStatus(422);
        $this->assertSame(0, Practice::count());
        auth()->logout();
        $this->start()->assertStatus(401);
    }

    public function test_assigned_staff_can_view_and_revocation_is_checked_again()
    {
        list($token, $path) = $this->completed();
        $this->post('/practices', $this->payload([$path]))->assertRedirect();
        $staff = $this->user('coach', 8);
        $staff->players()->attach($this->player->id);
        $this->actingAs($staff)->get('/practice-videos/' . $token)->assertOk();
        $staff->players()->detach($this->player->id);
        $this->get('/practice-videos/' . $token)->assertStatus(403);
    }

    public function test_invalid_content_size_expiration_and_cross_detail_paths_are_rejected()
    {
        $this->start(config('practice_video.max_size') + 1)->assertStatus(422);
        $token = $this->start()->assertOk()->json('token');
        $this->chunk($token, 0, str_repeat('x', 32))->assertOk();
        $this->postJson('/practice-videos/' . $token . '/complete')->assertStatus(422);
        PracticeVideoUpload::where('token', $token)->update(['expires_at' => now()->subDay()]);
        $this->chunk($token, 0, str_repeat('x', 32))->assertStatus(410);
        list($token, $path) = $this->completed();
        $this->post('/practices', $this->payload([$path]))->assertRedirect();
        $this->postJson('/practices', $this->payload([$path]))->assertStatus(422);
        $this->assertSame(1, Practice::count());
        $this->postJson('/practices', $this->payload(['../../.env']))->assertStatus(422);
    }

    public function test_legacy_url_is_preserved_and_new_menu_can_be_added()
    {
        $this->post('/practices', $this->payload([]))->assertRedirect();
        $practice = Practice::first();
        $detail = $practice->details()->first();
        $detail->update(['video_url' => 'https://example.test/old-video']);
        $payload = $this->payload(['https://example.test/old-video']);
        $payload['details'][0]['id'] = $detail->id;
        $payload['details'][] = ['menu_name' => '追加メニュー'];
        $this->patch('/practices/' . $practice->id, $payload)->assertRedirect();
        $this->assertCount(2, $practice->fresh()->details);
        $this->assertSame(['https://example.test/old-video'], json_decode($detail->fresh()->video_url, true));
        $this->get('/practices/' . $practice->id)->assertOk()->assertSee('https://example.test/old-video');
    }

    public function test_validation_failure_retains_completed_upload_and_target_switch_is_rejected()
    {
        list($token, $path) = $this->completed();
        $payload = $this->payload([$path]);
        $payload['practice_date'] = 'invalid';
        $this->from('/practices/create')->post('/practices', $payload)->assertSessionHasErrors('practice_date');
        $this->get('/practices/create')->assertOk()->assertSee($path);
        $this->assertSame('complete', PracticeVideoUpload::where('token', $token)->first()->status);
        $payload = $this->payload([$path]);
        $payload['player_id'] = 999;
        $this->postJson('/practices', $payload)->assertStatus(409);
    }

    public function test_cleanup_deletes_abandoned_and_deleted_practice_videos_only()
    {
        list($token, $path) = $this->completed();
        list($unusedToken, $unusedPath) = $this->completed();
        $this->post('/practices', $this->payload([$path]))->assertRedirect();
        $this->delete('/practices/' . Practice::first()->id)->assertRedirect();
        $this->get('/practice-videos/' . $token)->assertStatus(404);
        $this->artisan('practice-videos:cleanup')->assertExitCode(0);
        Storage::disk('local')->assertMissing($path);
        Storage::disk('local')->assertExists($unusedPath);
        PracticeVideoUpload::where('token', $unusedToken)->update(['expires_at' => now()->subDay()]);
        $this->artisan('practice-videos:cleanup')->assertExitCode(0);
        Storage::disk('local')->assertMissing($unusedPath);
    }
}
