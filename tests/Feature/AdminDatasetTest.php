<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDatasetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_datasets_page(): void
    {
        $this->get(route('admin.datasets'))->assertRedirect(route('login'));
    }

    public function test_valid_combined_dataset_is_marked_processed(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.datasets.store'), [
                'dataset' => $this->makeCombinedCsv('news.csv'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('datasets', [
            'original_name' => 'news.csv',
            'status' => 'processed',
        ]);
    }

    public function test_dataset_without_label_column_is_marked_failed(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();

        $content = "text,subject\n";
        for ($i = 0; $i < 200; $i++) {
            $content .= '"Some news article content number '.$i.'","news"'."\n";
        }

        $this->actingAs($admin)
            ->post(route('admin.datasets.store'), [
                'dataset' => UploadedFile::fake()->createWithContent('bad.csv', $content),
            ]);

        $this->assertDatabaseHas('datasets', [
            'original_name' => 'bad.csv',
            'status' => 'failed',
        ]);
    }

    public function test_admin_can_train_from_processed_dataset(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();

        $dataset = Dataset::create([
            'uploaded_by' => $admin->id,
            'filename' => Storage::putFileAs('datasets', $this->makeCombinedCsv('news.csv'), 'news.csv'),
            'original_name' => 'news.csv',
            'row_count' => 300,
            'status' => 'processed',
            'notes' => 'ready',
        ]);

        Http::fake([
            '*/train/start' => Http::response(['job_id' => 'ds-job', 'status' => 'running'], 202),
        ]);

        $rawDir = base_path('ml/data/raw');
        if (! is_dir($rawDir)) {
            mkdir($rawDir, 0777, true);
        }

        $this->actingAs($admin)
            ->post(route('admin.datasets.train', $dataset))
            ->assertRedirect(route('admin.training'));

        $this->assertDatabaseHas('training_jobs', [
            'started_by' => $admin->id,
            'status' => 'running',
        ]);
    }

    public function test_cannot_train_from_failed_dataset(): void
    {
        $admin = User::factory()->admin()->create();

        $dataset = Dataset::create([
            'uploaded_by' => $admin->id,
            'filename' => 'datasets/broken.csv',
            'original_name' => 'broken.csv',
            'row_count' => 0,
            'status' => 'failed',
            'notes' => 'No label column found.',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.datasets.train', $dataset))
            ->assertSessionHasErrors('dataset');

        $this->assertDatabaseCount('training_jobs', 0);
    }

    public function test_admin_can_delete_dataset(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();

        $dataset = Dataset::create([
            'uploaded_by' => $admin->id,
            'filename' => Storage::putFileAs('datasets', $this->makeCombinedCsv('news.csv'), 'news.csv'),
            'original_name' => 'news.csv',
            'row_count' => 300,
            'status' => 'processed',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.datasets.destroy', $dataset))
            ->assertRedirect();

        $this->assertDatabaseMissing('datasets', ['id' => $dataset->id]);
    }

    private function makeCombinedCsv(string $name): UploadedFile
    {
        $lines = ['text,label'];

        for ($i = 0; $i < 150; $i++) {
            $lines[] = '"Real news article content row '.$i.' about policy and economy.",REAL';
            $lines[] = '"Fake sensational hoax article row '.$i.' with wild claims.",FAKE';
        }

        return UploadedFile::fake()->createWithContent($name, implode("\n", $lines));
    }
}
