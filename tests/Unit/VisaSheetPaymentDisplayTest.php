<?php

namespace Tests\Unit;

use App\Http\Controllers\CRM\VisaTypeSheetController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisaSheetPaymentDisplayTest extends TestCase
{
    #[Test]
    public function lodged_sheet_keeps_amounts_over_one_thousand(): void
    {
        $html = Blade::render(<<<'BLADE'
            @php
                $paymentReceived = (float) str_replace(',', '', (string) ($total_payment ?? 0));
            @endphp
            @if($paymentReceived > 0)
                ${{ number_format($paymentReceived, 2) }}
            @endif
        BLADE, ['total_payment' => '3,850.00']);

        $this->assertStringContainsString('$3,850.00', $html);
        $this->assertStringNotContainsString('$3.00', $html);

        $sheet = file_get_contents(resource_path('views/crm/clients/sheets/visa-type-sheet.blade.php'));
        $this->assertIsString($sheet);
        $this->assertStringContainsString("str_replace(',', '', (string) (\$row->total_payment ?? 0))", $sheet);
    }

    #[Test]
    public function payment_total_is_a_plain_number_without_a_thousands_comma(): void
    {
        Schema::dropIfExists('account_client_receipts');
        Schema::create('account_client_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('client_matter_id')->nullable();
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);
            $table->unsignedTinyInteger('receipt_type')->nullable();
            $table->string('client_fund_ledger_type')->nullable();
            $table->string('save_type')->nullable();
            $table->unsignedTinyInteger('void_fee_transfer')->nullable();
            $table->unsignedTinyInteger('void_invoice')->nullable();
        });

        DB::table('account_client_receipts')->insert([
            'client_id' => 1,
            'client_matter_id' => 10,
            'deposit_amount' => 3850,
            'receipt_type' => 1,
            'client_fund_ledger_type' => 'Deposit',
        ]);

        $controller = new class extends VisaTypeSheetController
        {
            /**
             * @return array{total: float, pending: float}
             */
            public function payments(int $clientId, int $matterId): array
            {
                return $this->calculatePaymentsForMatter($clientId, $matterId);
            }
        };

        $payments = $controller->payments(1, 10);

        $this->assertSame(3850.0, $payments['total']);
        $this->assertStringNotContainsString(',', (string) $payments['total']);
    }
}
