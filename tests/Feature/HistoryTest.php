<?php

namespace Tests\Feature;

use App\Models\Prediction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_history(): void
    {
        $user = User::factory()->create();
        Prediction::create([
            'user_id' => $user->id,
            'input_text' => 'Sample news text for history listing test case here.',
            'result' => 'REAL',
            'confidence' => 90,
            'model_used' => 'svm',
        ]);

        $this->actingAs($user)
            ->get(route('history.index'))
            ->assertOk()
            ->assertSee('REAL');
    }

    public function test_user_can_delete_own_history(): void
    {
        $user = User::factory()->create();
        $prediction = Prediction::create([
            'user_id' => $user->id,
            'input_text' => 'Another sample news text for delete history test.',
            'result' => 'FAKE',
            'confidence' => 80,
            'model_used' => 'svm',
        ]);

        $this->actingAs($user)
            ->delete(route('history.destroy', $prediction))
            ->assertRedirect(route('history.index'));

        $this->assertDatabaseMissing('predictions', ['id' => $prediction->id]);
    }
}
