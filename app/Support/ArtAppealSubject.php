<?php

namespace App\Support;

use App\Models\ClientMatterReference;

/**
 * Visa or company matter recorded when an ART or EmpART catalogue file is opened.
 *
 * ART is a person file. EmpART is a company file. The choice is stored on the
 * ART sheet column refused_visa_type. Blank historical rows are left blank.
 */
final class ArtAppealSubject
{
    public const PERSON = 'person';

    public const COMPANY = 'company';

    public const REFERENCE_TYPE = 'art-matters';

    public static function kindFromNick(?string $nick): ?string
    {
        return match (strtolower(trim((string) $nick))) {
            'art' => self::PERSON,
            'empart' => self::COMPANY,
            default => null,
        };
    }

    /**
     * @return array<string, string>
     */
    public static function personOptions(): array
    {
        return [
            'student' => 'Student 500',
            'visitor' => 'Visitor 600',
            'tr' => '485',
            'partner' => 'Partner',
            'pr' => 'PR (189/190/491)',
            'employer-sponsored' => '482, 494, 186 or 407',
            'parent' => 'Parent',
            'other' => 'Other',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function companyOptions(): array
    {
        return [
            'sbs' => 'Standard Business Sponsorship',
            'nomination-core-skills' => 'Nomination (Core Skills)',
            'ens' => 'Employer Nomination Scheme',
            'regional-ens' => 'Regional Employer Nomination',
            'tas' => 'Temporary Activities Sponsorship',
            'training-nomination' => 'Training Nomination',
        ];
    }

    /**
     * Stored values from the previous mixed list. Shown so those rows keep a label
     * and can be changed on the client file. Not offered when a new matter is saved.
     *
     * @return array<string, string>
     */
    public static function legacyOptions(): array
    {
        return [
            'nomination' => 'Nomination',
            '407' => '407 Training Visa',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function sheetOptions(): array
    {
        return self::personOptions() + self::companyOptions() + self::legacyOptions();
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function sheetGroups(): array
    {
        return [
            'Person' => self::personOptions(),
            'Company' => self::companyOptions(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function optionsForKind(?string $kind): array
    {
        return match ($kind) {
            self::PERSON => self::personOptions(),
            self::COMPANY => self::companyOptions(),
            default => [],
        };
    }

    public static function rejectionForNick(?string $nick, mixed $value): ?string
    {
        $kind = self::kindFromNick($nick);
        if ($kind === null) {
            return null;
        }

        $selected = trim((string) $value);
        if ($selected !== '' && array_key_exists($selected, self::optionsForKind($kind))) {
            return null;
        }

        return $kind === self::COMPANY
            ? 'Choose the company matter this ART is about.'
            : 'Choose the visa this ART is about.';
    }

    /**
     * Options for the client file. Keeps an older stored value visible so it can be changed.
     *
     * @return array<string, string>
     */
    public static function detailOptions(?string $kind, ?string $current): array
    {
        $options = self::optionsForKind($kind);
        $current = trim((string) $current);
        if ($current !== '' && ! array_key_exists($current, $options)) {
            $options = [$current => self::sheetOptions()[$current] ?? $current] + $options;
        }

        return $options;
    }

    public static function rejectionForDetail(?string $nick, ?string $currentStored, mixed $value): ?string
    {
        $kind = self::kindFromNick($nick);
        if ($kind === null) {
            return 'This matter is not an ART file.';
        }

        $selected = trim((string) $value);
        if ($selected !== '' && array_key_exists($selected, self::detailOptions($kind, $currentStored))) {
            return null;
        }

        return self::rejectionForNick($nick, $selected);
    }

    public static function remember(int $clientId, int $clientMatterId, string $value, ?int $userId): void
    {
        $reference = ClientMatterReference::query()
            ->where('type', self::REFERENCE_TYPE)
            ->where('client_id', $clientId)
            ->where('client_matter_id', $clientMatterId)
            ->first();

        if ($reference) {
            $reference->refused_visa_type = $value;
            $reference->updated_by = $userId;
            $reference->save();

            return;
        }

        ClientMatterReference::query()->create([
            'type' => self::REFERENCE_TYPE,
            'client_id' => $clientId,
            'client_matter_id' => $clientMatterId,
            'refused_visa_type' => $value,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }
}
