<?php

namespace Tests\Feature;

use App\Models\TrainingJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTrainingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_training_page(): void
    {
        $this->get(route('admin.training'))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_training_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.training'))
            ->assertOk()
            ->assertSee('Model Training');
    }

    public function test_upload_requires_kaggle_filenames(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();

        $fakeCsv = UploadedFile::fake()->create('wrong-name.csv', 10, 'text/csv');

        $this->actingAs($admin)
            ->post(route('admin.training.upload'), [
                'fake_csv' => $fakeCsv,
                'true_csv' => UploadedFile::fake()->create('True.csv', 10, 'text/csv'),
            ])
            ->assertSessionHasErrors('fake_csv');
    }

    public function test_admin_can_upload_dataset_pair(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.training.upload'), [
                'fake_csv' => $this->makeCsvUpload('Fake.csv'),
                'true_csv' => $this->makeCsvUpload('True.csv'),
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertNotNull(session('training_upload_paths'));
    }

    public function test_start_training_calls_ml_api(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();

        $fakePath = 'training/test/Fake.csv';
        $truePath = 'training/test/True.csv';
        Storage::put($fakePath, $this->csvBody());
        Storage::put($truePath, $this->csvBody());

        session([
            'training_upload_paths' => [
                'fake' => $fakePath,
                'true' => $truePath,
            ],
        ]);

        Http::fake([
            '*/train/start' => Http::response(['job_id' => 'test-job', 'status' => 'running'], 202),
            '*/train/status' => Http::response([
                'status' => 'running',
                'progress' => 25,
                'stage' => 'Preprocessing text...',
            ], 200),
        ]);

        $rawDir = base_path('ml/data/raw');
        if (! is_dir($rawDir)) {
            mkdir($rawDir, 0777, true);
        }

        $this->actingAs($admin)
            ->post(route('admin.training.start'))
            ->assertRedirect(route('admin.training'));

        $this->assertDatabaseHas('training_jobs', [
            'started_by' => $admin->id,
            'status' => 'running',
        ]);
    }

    public function test_training_status_endpoint_returns_json(): void
    {
        $admin = User::factory()->admin()->create();

        TrainingJob::create([
            'started_by' => $admin->id,
            'ml_job_id' => 'remote-job',
            'status' => 'running',
            'progress' => 10,
            'stage' => 'Loading dataset...',
            'fake_csv_path' => 'training/a/Fake.csv',
            'true_csv_path' => 'training/a/True.csv',
            'started_at' => now(),
        ]);

        Http::fake([
            '*/train/status' => Http::response([
                'status' => 'running',
                'progress' => 45,
                'stage' => 'Training Logistic Regression...',
            ], 200),
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.training.status'))
            ->assertOk()
            ->assertJsonPath('progress', 45);
    }

    private function makeCsvUpload(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, $this->csvBody());
    }

    private function csvBody(): string
    {
        $lines = ["title,text,subject,date"];

        for ($i = 0; $i < 101; $i++) {
            $lines[] = '"Title '.$i.'","This is sample news article content for automated training tests row '.$i.'.","news","2024-01-01"';
        }

        return implode("\n", $lines);
    }
}
