<?php

namespace Tests\Unit;

use App\Support\OfficeReceiptServiceDescription;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OfficeReceiptServiceDescriptionTest extends TestCase
{
    #[Test]
    public function treats_matter_name_and_generic_words_as_generic(): void
    {
        Assert::assertTrue(OfficeReceiptServiceDescription::isGeneric('cash'));
        Assert::assertTrue(OfficeReceiptServiceDescription::isGeneric('Fees'));
        Assert::assertTrue(OfficeReceiptServiceDescription::isGeneric('485 - Temporary Graduate', '485 - Temporary Graduate'));
        Assert::assertFalse(OfficeReceiptServiceDescription::isGeneric('Professional fee — subclass 485 Temporary Graduate visa application'));
    }

    #[Test]
    public function keeps_specific_receipt_description_without_invoice_lookup(): void
    {
        $resolved = OfficeReceiptServiceDescription::resolve(
            'Professional fee — subclass 485 Temporary Graduate visa application',
            'INV-100',
            null,
            '485 - Temporary Graduate'
        );

        Assert::assertSame(
            'Professional fee — subclass 485 Temporary Graduate visa application',
            $resolved
        );
    }

    #[Test]
    public function uses_invoice_line_items_when_the_receipt_description_is_generic(): void
    {
        Schema::create('account_all_invoice_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('receipt_type')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('description')->nullable();
        });
        Schema::create('account_client_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('receipt_type')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('description')->nullable();
        });

        DB::table('account_client_receipts')->insert([
            'client_id' => 1,
            'receipt_type' => 3,
            'invoice_no' => 'INV-10',
            'payment_type' => 'Surcharge',
            'description' => 'Credit Card Surcharge',
        ]);
        DB::table('account_all_invoice_receipts')->insert([
            [
                'client_id' => 1,
                'receipt_type' => 3,
                'invoice_no' => 'INV-10',
                'payment_type' => 'Surcharge',
                'description' => 'Credit Card Surcharge',
            ],
            [
                'client_id' => 1,
                'receipt_type' => 3,
                'invoice_no' => 'INV-10',
                'payment_type' => 'Professional Fee',
                'description' => 'Professional fee — subclass 485 Temporary Graduate visa application',
            ],
            [
                'client_id' => 1,
                'receipt_type' => 3,
                'invoice_no' => 'INV-10',
                'payment_type' => 'Department Charges',
                'description' => 'Department charge — subclass 485 Temporary Graduate visa application',
            ],
        ]);

        $resolved = OfficeReceiptServiceDescription::resolve('fees', 'INV-10', 1, '485 - Temporary Graduate');

        Assert::assertSame(
            'Professional fee — subclass 485 Temporary Graduate visa application; Department charge — subclass 485 Temporary Graduate visa application; Credit Card Surcharge',
            $resolved
        );
    }
}
