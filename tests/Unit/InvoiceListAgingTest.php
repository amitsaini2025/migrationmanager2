<?php

namespace Tests\Unit;

use App\Support\InvoiceListAging;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceListAgingTest extends TestCase
{
    private const TODAY = '2026-11-06';

    protected function setUp(): void
    {
        parent::setUp();

        $schema = $this->app->make('db')->connection()->getSchemaBuilder();
        $schema->dropIfExists('invoice_list_aging_rows');
        $schema->create('invoice_list_aging_rows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('trans_no')->nullable();
            $table->string('trans_date')->nullable();
            $table->string('payment_type')->nullable();
            $table->decimal('balance_amount', 12, 2)->nullable();
            $table->unsignedInteger('invoice_status')->nullable();
            $table->unsignedInteger('void_invoice')->nullable();
            $table->unsignedInteger('receipt_type')->nullable();
        });
    }

    #[Test]
    public function critical_filter_keeps_unpaid_invoices_older_than_90_days(): void
    {
        $this->insert('critical-unpaid', '07/08/2026', 100, 0, 0);
        $this->insert('paid-old', '07/08/2026', 0, 1, 0);
        $this->insert('paid-with-balance', '01/01/2026', 500, 1, 0);
        $this->insert('void-old', '01/01/2026', 500, 0, 1);
        $this->insert('zero-balance', '01/01/2026', 0, 0, 0);
        $this->insert('urgent', '06/09/2026', 40, 2, 0);
        $this->insert('current', '05/11/2026', 250, 0, 0);
        $this->insert('discount-critical', '07/08/2026', -80, 0, 0, 'Discount');

        Assert::assertSame(
            ['critical-unpaid', 'discount-critical'],
            $this->filtered('critical')
        );
    }

    #[Test]
    public function age_bands_follow_the_badge_boundaries(): void
    {
        $this->insert('day-30', '07/10/2026', 10, 0, 0);
        $this->insert('day-31', '06/10/2026', 10, 0, 0);
        $this->insert('day-60', '07/09/2026', 10, 0, 0);
        $this->insert('day-61', '06/09/2026', 10, 0, 0);
        $this->insert('day-90', '08/08/2026', 10, 0, 0);
        $this->insert('day-91', '07/08/2026', 10, 0, 0);
        $this->insert('blank-date', '', 10, 0, 0);
        $this->insert('future', '07/11/2026', 10, 0, 0);

        Assert::assertSame(['blank-date', 'day-30', 'future'], $this->filtered('current'));
        Assert::assertSame(['day-31', 'day-60'], $this->filtered('warning'));
        Assert::assertSame(['day-61', 'day-90'], $this->filtered('urgent'));
        Assert::assertSame(['day-91'], $this->filtered('critical'));
    }

    #[Test]
    public function empty_aging_category_does_not_change_the_query(): void
    {
        $open = DB::table('invoice_list_aging_rows')->where('client_id', 9);
        $untouched = DB::table('invoice_list_aging_rows')->where('client_id', 9);
        InvoiceListAging::apply($untouched, '');
        InvoiceListAging::apply($untouched, null);
        InvoiceListAging::apply($untouched, 'paid');

        Assert::assertSame($open->toSql(), $untouched->toSql());
        Assert::assertSame($open->getBindings(), $untouched->getBindings());
    }

    #[Test]
    public function aging_filter_keeps_an_existing_client_constraint(): void
    {
        $this->insert('client-a', '07/08/2026', 100, 0, 0, 'Professional Fee', 4);
        $this->insert('client-b', '07/08/2026', 100, 0, 0, 'Professional Fee', 5);

        $query = DB::table('invoice_list_aging_rows')->where('client_id', 4);
        InvoiceListAging::apply($query, 'critical', self::TODAY);

        Assert::assertSame(['client-a'], $query->orderBy('trans_no')->pluck('trans_no')->all());
    }

    #[Test]
    public function presentation_matches_the_filter_buckets(): void
    {
        $paid = (object) [
            'trans_date' => '05/11/2026',
            'balance_amount' => 0,
            'invoice_status' => 1,
            'void_invoice' => 0,
            'payment_type' => 'Professional Fee',
        ];
        $critical = (object) [
            'trans_date' => '07/08/2026',
            'balance_amount' => 100,
            'invoice_status' => 0,
            'void_invoice' => 0,
            'payment_type' => 'Professional Fee',
        ];

        Assert::assertSame('Paid', InvoiceListAging::presentation($paid, self::TODAY)['label']);
        Assert::assertNull(InvoiceListAging::bucket($paid, self::TODAY));
        Assert::assertSame('Critical', InvoiceListAging::presentation($critical, self::TODAY)['label']);
        Assert::assertSame('critical', InvoiceListAging::bucket($critical, self::TODAY));
    }

    /**
     * @return array<int, string>
     */
    private function filtered(string $category): array
    {
        $query = DB::table('invoice_list_aging_rows');
        InvoiceListAging::apply($query, $category, self::TODAY);

        return $query->orderBy('trans_no')->pluck('trans_no')->all();
    }

    private function insert(
        string $transNo,
        string $transDate,
        float $balance,
        int $invoiceStatus,
        int $voidInvoice,
        string $paymentType = 'Professional Fee',
        int $clientId = 1,
    ): void {
        DB::table('invoice_list_aging_rows')->insert([
            'client_id' => $clientId,
            'trans_no' => $transNo,
            'trans_date' => $transDate,
            'payment_type' => $paymentType,
            'balance_amount' => $balance,
            'invoice_status' => $invoiceStatus,
            'void_invoice' => $voidInvoice,
            'receipt_type' => 3,
        ]);
    }
}
