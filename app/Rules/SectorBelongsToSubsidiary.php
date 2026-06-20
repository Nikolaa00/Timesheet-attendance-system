<?php

namespace App\Rules;

use App\Models\Sector;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SectorBelongsToSubsidiary implements ValidationRule
{
    public const MESSAGE = 'Sector does not exist for the selected subsidiary.';

    public const SUBSIDIARY_REQUIRED_MESSAGE = 'A subsidiary must be selected when a sector is assigned.';

    public function __construct(
        private readonly mixed $subsidiaryId = null,
        private readonly mixed $sectorId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $subsidiaryId = $this->subsidiaryId ?? ($attribute === 'subsidiary_id' ? $value : null);
        $sectorId = $this->sectorId ?? ($attribute === 'sector_id' ? $value : null);

        if ($sectorId !== null && ($subsidiaryId === null || $subsidiaryId === '')) {
            $fail(self::SUBSIDIARY_REQUIRED_MESSAGE);

            return;
        }

        if ($subsidiaryId === null || $sectorId === null) {
            return;
        }

        $exists = Sector::query()
            ->whereKey($sectorId)
            ->whereHas('subsidiaries', fn ($query) => $query->whereKey($subsidiaryId))
            ->exists();

        if (! $exists) {
            $fail(self::MESSAGE);
        }
    }
}
