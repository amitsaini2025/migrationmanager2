<?php

namespace Tests\Unit;

use App\Models\ClientEoiReference;
use App\Services\ExpireSubmittedEoisService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpireSubmittedEoisServiceTest extends TestCase
{
    private ExpireSubmittedEoisService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
        $this->service = new ExpireSubmittedEoisService;
    }

    #[Test]
    public function it_expires_only_submitted_eois_older_than_two_years(): void
    {
        $asOf = Carbon::parse('2026-11-02');

        $this->insertEoi(1, 'submitted', '2024-11-02'); // exactly 2 years
        $this->insertEoi(2, 'submitted', '2024-11-01'); // older than 2 years
        $this->insertEoi(3, 'submitted', '2024-11-03'); // under 2 years
        $this->insertEoi(4, 'invited', '2023-01-01'); // other status kept
        $this->insertEoi(5, 'draft', '2020-01-01'); // draft kept
        $this->insertEoi(6, 'submitted', null); // missing date kept

        $count = $this->service->expire($asOf);

        $this->assertSame(2, $count);
        $this->assertSame('expired', DB::table('client_eoi_references')->where('id', 1)->value('eoi_status'));
        $this->assertSame('expired', DB::table('client_eoi_references')->where('id', 2)->value('eoi_status'));
        $this->assertSame('submitted', DB::table('client_eoi_references')->where('id', 3)->value('eoi_status'));
        $this->assertSame('invited', DB::table('client_eoi_references')->where('id', 4)->value('eoi_status'));
        $this->assertSame('draft', DB::table('client_eoi_references')->where('id', 5)->value('eoi_status'));
        $this->assertSame('submitted', DB::table('client_eoi_references')->where('id', 6)->value('eoi_status'));
    }

    #[Test]
    public function effective_status_marks_overdue_submitted_as_expired_without_db_write(): void
    {
        $asOf = Carbon::parse('2026-11-02');

        $this->assertSame(
            'expired',
            $this->service->effectiveStatus('submitted', '2024-11-01', $asOf)
        );
        $this->assertSame(
            'submitted',
            $this->service->effectiveStatus('submitted', '2024-11-03', $asOf)
        );
        $this->assertSame(
            'invited',
            $this->service->effectiveStatus('invited', '2020-01-01', $asOf)
        );
    }

    #[Test]
    public function command_expires_submitted_eois(): void
    {
        $this->insertEoi(10, 'submitted', now()->subYears(3)->toDateString());
        $this->insertEoi(11, 'submitted', now()->subMonth()->toDateString());

        $this->artisan('eoi:expire-submitted')
            ->expectsOutputToContain('Expired 1 submitted EOI record(s).')
            ->assertSuccessful();

        $this->assertSame('expired', DB::table('client_eoi_references')->where('id', 10)->value('eoi_status'));
        $this->assertSame('submitted', DB::table('client_eoi_references')->where('id', 11)->value('eoi_status'));
    }

    private function createSchema(): void
    {
        Schema::dropIfExists('client_eoi_references');

        Schema::create('client_eoi_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('eoi_status')->default(ClientEoiReference::STATUS_DRAFT);
            $table->date('EOI_submission_date')->nullable();
            $table->timestamps();
        });
    }

    private function insertEoi(int $id, string $status, ?string $submissionDate): void
    {
        DB::table('client_eoi_references')->insert([
            'id' => $id,
            'client_id' => 1,
            'eoi_status' => $status,
            'EOI_submission_date' => $submissionDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
