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

    /**
     * The ML service response for a claim the fact layer contradicts: the style
     * model said REAL, the fact layer overrode it to FAKE.
     */
    private function factCheckOverrideResponse(): array
    {
        return [
            'label' => 'FAKE',
            'confidence' => 95.0,
            'confidence_level' => 'HIGH',
            'model' => 'logistic_regression',
            'probabilities' => ['REAL' => 78.0, 'FAKE' => 22.0],
            'style_label' => 'REAL',
            'style_confidence' => 78.0,
            'verdict_source' => 'fact_check',
            'fact_check' => [
                'checked' => true,
                'verdict' => 'CONTRADICTED',
                'confidence' => 95.0,
                'claims' => [
                    ['subject' => 'Babar Azam', 'role' => 'prime minister', 'place' => 'Pakistan'],
                ],
                'evidence' => [[
                    'source' => 'local_kb',
                    'verdict' => 'CONTRADICTED',
                    'confidence' => 95.0,
                    'statement' => 'Mohammad Babar Azam is a cricketer, not the prime minister of Pakistan.',
                    'url' => 'https://www.wikidata.org/wiki/Q23767766',
                    'rating' => 'False',
                ]],
                'sources_checked' => ['local_kb'],
                'degraded' => false,
            ],
            'signals' => [
                [
                    'id' => 'short_input',
                    'label' => 'Very short text',
                    'tone' => 'neutral',
                    'detail' => 'Short claims give the AI little to work with.',
                ],
            ],
            'scores' => ['style' => 100, 'source' => 55, 'model' => 78],
            'explanation' => [
                'headline' => 'This contradicts our records',
                'plain' => 'We checked the claim against known facts and found it does not match.',
                'bullets' => ['Mohammad Babar Azam is a cricketer, not the prime minister of Pakistan.'],
                'disclaimer' => 'This tool checks writing style and a limited database of known facts.',
            ],
        ];
    }

    public function test_fact_check_override_is_the_verdict_that_gets_stored(): void
    {
        Http::fake(['*/predict' => Http::response($this->factCheckOverrideResponse(), 200)]);

        $text = "Babar Azam is Pakistan's Prime Minister and will address the nation tonight.";

        $this->post(route('news.check'), ['text' => $text])->assertOk();

        // The style model said REAL; the stored verdict must be the final one.
        $this->assertDatabaseHas('predictions', [
            'user_id' => null,
            'result' => 'FAKE',
            'confidence' => 95.0,
        ]);
    }

    public function test_json_response_passes_through_fact_check_fields(): void
    {
        Http::fake(['*/predict' => Http::response($this->factCheckOverrideResponse(), 200)]);

        $text = "Babar Azam is Pakistan's Prime Minister and will address the nation tonight.";

        $this->postJson(route('news.check'), ['text' => $text])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('result.label', 'FAKE')
            ->assertJsonPath('result.verdict_source', 'fact_check')
            ->assertJsonPath('result.style_label', 'REAL')
            ->assertJsonPath('result.fact_check.verdict', 'CONTRADICTED')
            ->assertJsonPath('result.explanation.headline', 'This contradicts our records')
            ->assertJsonStructure([
                'result' => [
                    'signals' => [['id', 'label', 'tone', 'detail']],
                    'scores' => ['style', 'source', 'model'],
                    'fact_check' => ['evidence', 'sources_checked', 'degraded'],
                    'explanation' => ['headline', 'plain', 'bullets', 'disclaimer'],
                ],
            ]);
    }

    public function test_result_page_shows_fact_check_evidence_and_disclaimer(): void
    {
        Http::fake(['*/predict' => Http::response($this->factCheckOverrideResponse(), 200)]);

        $text = "Babar Azam is Pakistan's Prime Minister and will address the nation tonight.";

        $this->post(route('news.check'), ['text' => $text])
            ->assertOk()
            ->assertSee('This contradicts our records')
            ->assertSee('Mohammad Babar Azam is a cricketer, not the prime minister of Pakistan.')
            ->assertSee('https://www.wikidata.org/wiki/Q23767766')
            ->assertSee('VeriFact fact database')
            ->assertSee('This tool checks writing style and a limited database of known facts.');
    }

    public function test_result_page_still_renders_without_fact_check_fields(): void
    {
        // Guards against a stale ML service that predates the fact layer.
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
            ->assertOk()
            ->assertSee('REAL');
    }
}
