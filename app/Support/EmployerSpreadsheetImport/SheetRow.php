<?php

namespace App\Support\EmployerSpreadsheetImport;

final class SheetRow
{
    /**
     * @param  list<string>  $emails
     */
    public function __construct(
        public string $source,
        public string $matterId,
        public ?string $business,
        public ?string $trading,
        public string $role,
        public ?string $surname,
        public ?string $given,
        public array $emails,
        public ?string $phone,
        public ?string $matterType,
        public ?string $fileStatus,
        public ?string $dob,
        public ?string $position,
        public ?string $anzsco,
        public ?string $responsible,
        public ?string $engagementOn,
        public ?string $passportCountry,
        public ?string $passportExpiry,
        public ?string $maritalStatus,
    ) {}
}
