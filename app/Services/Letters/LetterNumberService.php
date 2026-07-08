<?php

namespace App\Services\Letters;

use App\Models\LetterCounter;
use App\Models\LetterOutgoing;
use App\Models\LetterType;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LetterNumberService
{
    public const DEFAULT_SCHOOL_CODE = 'SMA-PERSIS-SRG';

    public function generate(LetterType|int $letterType, CarbonInterface|string|null $date = null, ?string $classificationCode = null, ?string $schoolCode = null): array
    {
        $type = $letterType instanceof LetterType
            ? $letterType
            : LetterType::query()->findOrFail($letterType);

        $letterDate = $date instanceof CarbonInterface
            ? CarbonImmutable::instance($date)
            : CarbonImmutable::parse($date ?? now());

        return DB::transaction(function () use ($type, $letterDate, $classificationCode, $schoolCode) {
            $counter = $this->lockCounter((int) $letterDate->year, $type->id);
            $counter->last_number = max($counter->last_number, $this->maxIssuedSequence((int) $letterDate->year));
            $counter->last_number++;
            $counter->month = (int) $letterDate->month;
            $counter->updated_by = Auth::id();
            $counter->save();

            $sequence = $counter->last_number;

            return [
                'sequence_number' => $sequence,
                'letter_number' => $this->formatNumber($sequence, $type->code, $letterDate, $classificationCode, $schoolCode),
                'letter_type_id' => $type->id,
                'year' => (int) $letterDate->year,
                'month' => (int) $letterDate->month,
            ];
        });
    }

    public function formatNumber(int $sequence, string $typeCode, CarbonInterface|string|null $date = null, ?string $classificationCode = null, ?string $schoolCode = null): string
    {
        $letterDate = $date instanceof CarbonInterface
            ? CarbonImmutable::instance($date)
            : CarbonImmutable::parse($date ?? now());

        $classificationCode = $classificationCode ?: '421.3';
        $schoolCode = $schoolCode ?: self::DEFAULT_SCHOOL_CODE;

        return sprintf(
            '%s/%s/%s/%s/%s',
            $classificationCode,
            str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
            $schoolCode,
            $this->romanMonth((int) $letterDate->month),
            $letterDate->year
        );
    }

    public function romanMonth(int $month): string
    {
        return [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ][$month] ?? '';
    }

    private function lockCounter(int $year, int $letterTypeId): LetterCounter
    {
        $counter = LetterCounter::query()
            ->where('year', $year)
            ->where('letter_type_id', $letterTypeId)
            ->lockForUpdate()
            ->first();

        if ($counter) {
            return $counter;
        }

        try {
            return LetterCounter::query()->create([
                'letter_type_id' => $letterTypeId,
                'year' => $year,
                'month' => null,
                'last_number' => 0,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        } catch (QueryException) {
            return LetterCounter::query()
                ->where('year', $year)
                ->where('letter_type_id', $letterTypeId)
                ->lockForUpdate()
                ->firstOrFail();
        }
    }

    private function maxIssuedSequence(int $year): int
    {
        return (int) LetterOutgoing::query()
            ->where('letter_year', $year)
            ->whereNotNull('sequence_number')
            ->max('sequence_number');
    }
}
