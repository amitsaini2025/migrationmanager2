<?php

namespace App\Support\EmployerSpreadsheetImport;

final class CompanyContactRow
{
    /**
     * @param  list<array{surname: ?string, given: ?string, position: ?string, type: ?string, dob: ?string, phone: ?string, email: ?string}>  $contacts
     */
    public function __construct(
        public string $matterId,
        public ?string $business,
        public ?string $trading,
        public ?string $matterPhone,
        public ?string $contactBusinessName,
        public ?string $contactTrading,
        public ?string $abn,
        public ?string $acn,
        public ?string $structure,
        public ?string $industry,
        public ?string $startDate,
        public ?string $businessPhone,
        public ?string $businessMobile,
        public ?string $businessEmail,
        public ?string $street1,
        public ?string $street2,
        public ?string $suburb,
        public ?string $state,
        public ?string $postcode,
        public ?string $country,
        public array $contacts,
    ) {}

    /**
     * @return list<string>
     */
    public function matchNames(): array
    {
        return array_values(array_filter([
            $this->business,
            $this->trading,
            $this->contactBusinessName,
            $this->contactTrading,
        ]));
    }
}
