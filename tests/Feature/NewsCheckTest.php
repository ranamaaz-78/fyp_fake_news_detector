<?php

namespace Tests\Feature;

use App\Models\Prediction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_check_news_validates_minimum_length(): void
    {
        $this->post(route('news.check'), ['text' => 'short'])
            ->assertSessionHasErrors('text');
    }

    public function test_check_news_returns_result_with_mocked_ml_api(): void
    {
        Http::fake([
            '*/predict' => Http::response([
                'label' => 'REAL',
                'confidence' => 92.5,
                'confidence_level' => 'HIGH',
                'model' => 'svm',
            ], 200),
        ]);

        $text = str_repeat('Official government report confirms policy update after review. ', 2);

        $this->post(route('news.check'), ['text' => $text])
            ->assertOk()
            ->assertSee('REAL');
    }

    public function test_logged_in_user_prediction_is_saved(): void
    {
        Http::fake([
            '*/predict' => Http::response([
                'label' => 'FAKE',
                'confidence' => 88.0,
                'confidence_level' => 'HIGH',
                'model' => 'svm',
            ], 200),
        ]);

        $user = User::factory()->create();
        $text = str_repeat('Shocking miracle cure hidden by doctors according to viral post. ', 2);

        $this->actingAs($user)
            ->post(route('news.check'), ['text' => $text])
            ->assertOk();

        $this->assertDatabaseHas('predictions', [
            'user_id' => $user->id,
            'result' => 'FAKE',
        ]);
    }

    public function test_guest_prediction_is_saved(): void
    {
        Http::fake([
            '*/predict' => Http::response([
                'label' => 'REAL',
                'confidence' => 90.0,
                'confidence_level' => 'HIGH',
                'model' => 'svm',
            ], 200),
        ]);

        $text = str_repeat('Official government report confirms policy update after review. ', 2);

        $this->post(route('news.check'), ['text' => $text])
            ->assertOk();

        $this->assertDatabaseHas('predictions', [
            'user_id' => null,
            'result' => 'REAL',
        ]);
    }

    public function test_check_news_via_url_validates_url(): void
    {
        $this->post(route('news.check'), ['url' => 'not-a-url'])
            ->assertSessionHasErrors('url');
    }

    public function test_check_news_via_url_extracts_and_predicts(): void
    {
        $articleText = str_repeat('Breaking news report from the capital confirms official policy changes today. ', 2);

        Http::fake([
            'example.com/*' => Http::response(
                '<html><body><article><p>'.e($articleText).'</p></article></body></html>',
                200,
                ['Content-Type' => 'text/html']
            ),
            '*/predict' => Http::response([
                'label' => 'REAL',
                'confidence' => 91.0,
                'confidence_level' => 'HIGH',
                'model' => 'svm',
            ], 200),
        ]);

        $this->post(route('news.check'), ['url' => 'https://example.com/news/story'])
            ->assertOk()
            ->assertSee('REAL');

        $this->assertDatabaseHas('predictions', [
            'user_id' => null,
            'result' => 'REAL',
        ]);
    }

    public function test_check_news_via_url_rejects_short_extraction(): void
    {
        Http::fake([
            'example.com/*' => Http::response(
                '<html><body><article><p>Too short.</p></article></body></html>',
                200,
                ['Content-Type' => 'text/html']
            ),
        ]);

        $this->post(route('news.check'), ['url' => 'https://example.com/empty'])
            ->assertSessionHasErrors('url');

        $this->assertSame(0, Prediction::count());
    }

    public function test_check_news_rejects_both_text_and_url(): void
    {
        $text = str_repeat('Official government report confirms policy update after review. ', 2);

        $this->post(route('news.check'), [
            'text' => $text,
            'url' => 'https://example.com/story',
        ])->assertSessionHasErrors('text');
    }
}
