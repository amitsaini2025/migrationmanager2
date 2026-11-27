<?php

namespace Tests\Unit;

use App\Support\StatementOfServices;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StatementOfServicesTest extends TestCase
{
    #[Test]
    public function lines_reduce_each_invoice_charge_by_its_office_adjustment(): void
    {
        $rows = [
            (object) [
                'trans_date' => '01/03/2026',
                'description' => 'Professional fee — subclass 485 Temporary Graduate visa application',
                'withdraw_amount' => 110,
                'invoice_no' => 'INV-1',
                'trans_no' => 'INV-1',
            ],
            (object) [
                'trans_date' => '02/03/2026',
                'description' => 'Professional fee — subclass 500 Student visa application',
                'withdraw_amount' => 60,
                'invoice_no' => 'INV-2',
                'trans_no' => 'INV-2',
            ],
            (object) [
                'trans_date' => '02/03/2026',
                'description' => 'Professional fee — additional student service',
                'withdraw_amount' => 40,
                'invoice_no' => 'INV-2',
                'trans_no' => 'INV-2',
            ],
        ];

        $lines = StatementOfServices::lines($rows, [
            'INV-1' => 10,
            'INV-2' => 70,
        ]);

        $this->assertCount(2, $lines);
        $this->assertSame(100.0, $lines[0]['charge']);
        $this->assertSame(30.0, $lines[1]['charge']);
        $this->assertSame('INV-1', $lines[0]['invoice_no']);
        $this->assertSame('INV-2', $lines[1]['invoice_no']);
        $this->assertSame('Professional fee — subclass 485 Temporary Graduate visa application', $lines[0]['description']);
        $this->assertSame('Professional fee — subclass 500 Student visa application', $lines[1]['description']);
    }

    #[Test]
    public function lines_are_ordered_by_service_date_and_keep_same_day_order(): void
    {
        $rows = [
            (object) [
                'trans_date' => '15/04/2026',
                'description' => 'Later service',
                'withdraw_amount' => 20,
                'invoice_no' => 'INV-B',
                'trans_no' => 'INV-B',
            ],
            (object) [
                'trans_date' => '01/02/2026',
                'description' => 'Earlier service',
                'withdraw_amount' => 10,
                'invoice_no' => 'INV-A',
                'trans_no' => 'INV-A',
            ],
            (object) [
                'trans_date' => '01/02/2026',
                'description' => 'Same day second service',
                'withdraw_amount' => 5,
                'invoice_no' => 'INV-C',
                'trans_no' => 'INV-C',
            ],
        ];

        $lines = StatementOfServices::lines($rows, []);

        $this->assertSame('Earlier service', $lines[0]['description']);
        $this->assertSame('Same day second service', $lines[1]['description']);
        $this->assertSame('Later service', $lines[2]['description']);
    }

    #[Test]
    public function lines_group_one_row_per_invoice_including_disbursements_and_discounts(): void
    {
        $rows = [
            (object) [
                'trans_date' => '01/03/2026',
                'description' => 'Professional fee — subclass 485',
                'withdraw_amount' => 110,
                'payment_type' => 'Professional Fee',
                'invoice_no' => 'INV-1',
                'trans_no' => 'INV-1',
            ],
            (object) [
                'trans_date' => '01/03/2026',
                'description' => 'ART Fees',
                'withdraw_amount' => 65,
                'payment_type' => 'Disbursements',
                'invoice_no' => 'INV-1',
                'trans_no' => 'INV-1',
            ],
            (object) [
                'trans_date' => '02/03/2026',
                'description' => 'Department charge — visa application',
                'withdraw_amount' => 50,
                'payment_type' => 'Department Charges',
                'invoice_no' => 'INV-1',
                'trans_no' => 'INV-1',
            ],
            (object) [
                'trans_date' => '02/03/2026',
                'description' => 'Discount',
                'withdraw_amount' => 10,
                'payment_type' => 'Discount',
                'invoice_no' => 'INV-1',
                'trans_no' => 'INV-1',
            ],
        ];

        $lines = StatementOfServices::lines($rows, [
            'INV-1' => 30,
        ]);

        $this->assertCount(1, $lines);
        $this->assertSame('INV-1', $lines[0]['invoice_no']);
        $this->assertSame('Professional fee — subclass 485', $lines[0]['description']);
        $this->assertSame('01/03/2026', $lines[0]['date']);
        $this->assertSame(185.0, $lines[0]['charge']);
    }

    #[Test]
    public function office_adjustment_reduces_the_professional_fee_before_a_disbursement(): void
    {
        $rows = [
            (object) [
                'trans_date' => '01/03/2026',
                'description' => 'Printing and Postage Charges',
                'withdraw_amount' => 0.88,
                'payment_type' => 'Disbursements',
                'invoice_no' => 'INV-1',
                'trans_no' => 'INV-1',
            ],
            (object) [
                'trans_date' => '01/03/2026',
                'description' => 'Professional fee — subclass 485',
                'withdraw_amount' => 110,
                'payment_type' => 'Professional Fee',
                'invoice_no' => 'INV-1',
                'trans_no' => 'INV-1',
            ],
        ];

        $lines = StatementOfServices::lines($rows, [
            'INV-1' => 10,
        ]);

        $this->assertCount(1, $lines);
        $this->assertSame('Professional fee — subclass 485', $lines[0]['description']);
        $this->assertSame(100.88, $lines[0]['charge']);
        $this->assertSame('INV-1', $lines[0]['invoice_no']);
    }

    #[Test]
    public function statement_blade_names_each_service_and_its_charge(): void
    {
        $blade = (string) file_get_contents(resource_path('views/emails/statement-of-services.blade.php'));

        $this->assertStringContainsString('Statement of services', $blade);
        $this->assertStringContainsString('This statement sets out each invoice and the charge for that invoice.', $blade);
        $this->assertStringContainsString('MARN', $blade);
        $this->assertStringContainsString('Invoice No', $blade);
        $this->assertStringContainsString('$line[\'description\']', $blade);
        $this->assertStringContainsString('$line[\'charge\']', $blade);
    }

    #[Test]
    public function account_tab_offers_statement_of_services_for_the_open_matter(): void
    {
        $blade = (string) file_get_contents(resource_path('views/crm/clients/tabs/account.blade.php'));

        $this->assertStringContainsString('Statement of services', $blade);
        $this->assertStringContainsString('clients.statementOfServices', $blade);
        $this->assertStringContainsString('client_selected_matter_id', $blade);
        $this->assertStringContainsString('statement-of-services-row', $blade);
        $this->assertStringContainsString('send-statement-to-client', $blade);
        $this->assertStringContainsString('>Invoice</div>', $blade);
    }
}
