<?php

namespace Tests\Unit;

use App\Models\LetterCounter;
use App\Models\LetterType;
use App\Models\User;
use App\Services\Letters\LetterNumberService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LetterNumberServiceTest extends TestCase
{
    use DatabaseTransactions;

    private LetterNumberService $service;
    private LetterType $type;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LetterNumberService::class);
        $this->type = LetterType::factory()->create(['code' => 'TST']);
        $this->user = User::factory()->create();

        $this->actingAs($this->user);
    }

    public function test_generates_first_number_when_counter_does_not_exist(): void
    {
        $number = $this->service->generate($this->type, '2026-07-01');

        $this->assertEquals(1, $number['sequence_number']);
        $this->assertStringContainsString('001', $number['letter_number']);
        $this->assertEquals('2026', $number['year']);
        $this->assertEquals(7, $number['month']);

        $counter = LetterCounter::where('year', 2026)
            ->where('letter_type_id', $this->type->id)
            ->first();
        $this->assertNotNull($counter);
        $this->assertEquals(1, $counter->last_number);
    }

    public function test_generates_sequential_numbers_when_counter_exists(): void
    {
        // First issue
        $first = $this->service->generate($this->type, '2026-07-01');
        $this->assertEquals(1, $first['sequence_number']);

        // Second issue
        $second = $this->service->generate($this->type, '2026-07-01');
        $this->assertEquals(2, $second['sequence_number']);

        // Third issue
        $third = $this->service->generate($this->type, '2026-07-01');
        $this->assertEquals(3, $third['sequence_number']);

        // Counter updated
        $counter = LetterCounter::where('year', 2026)
            ->where('letter_type_id', $this->type->id)
            ->first();
        $this->assertEquals(3, $counter->last_number);
    }

    public function test_separates_counters_by_year(): void
    {
        $first = $this->service->generate($this->type, '2026-07-01');
        $this->assertEquals(1, $first['sequence_number']);

        $second = $this->service->generate($this->type, '2027-01-01');
        $this->assertEquals(1, $second['sequence_number']);
    }

    public function test_separates_counters_by_letter_type(): void
    {
        $otherType = LetterType::factory()->create(['code' => 'OTR']);

        $first = $this->service->generate($this->type, '2026-07-01');
        $this->assertEquals(1, $first['sequence_number']);

        $other = $this->service->generate($otherType, '2026-07-01');
        $this->assertEquals(1, $other['sequence_number']);
    }

    public function test_initially_empty_counter_gets_max_issued_sequence(): void
    {
        // Manually set sequence_number in letter_outgoings (simulate historical data)
        $this->service->generate($this->type, '2026-07-01');

        // Delete the counter to simulate fresh start
        LetterCounter::where('year', 2026)
            ->where('letter_type_id', $this->type->id)
            ->delete();

        // Re-issue should start from max issued + 1
        $number = $this->service->generate($this->type, '2026-07-01');
        $this->assertEquals(2, $number['sequence_number']);
    }
}
