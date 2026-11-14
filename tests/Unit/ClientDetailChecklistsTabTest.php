<?php

namespace Tests\Unit;

use App\Models\CostAssignmentForm;
use App\Support\ClientDetailChecklistsTab;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @property mixed $app
 */
class ClientDetailChecklistsTabTest extends TestCase
{
    #[Test]
    public function build_returns_staff_office_and_matter_dropdown_payloads(): void
    {
        $this->createChecklistsTabSchema();

        DB::table('staff')->insert([
            [
                'id' => 10,
                'first_name' => 'Mig',
                'last_name' => 'Agent',
                'email' => 'mig@example.com',
                'role' => 16,
                'status' => 1,
                'is_migration_agent' => 1,
                'office_id' => 1,
            ],
            [
                'id' => 11,
                'first_name' => 'Person',
                'last_name' => 'Responsible',
                'email' => 'pr@example.com',
                'role' => 12,
                'status' => 1,
                'is_migration_agent' => 0,
                'office_id' => 1,
            ],
            [
                'id' => 12,
                'first_name' => 'Person',
                'last_name' => 'Assisting',
                'email' => 'pa@example.com',
                'role' => 13,
                'status' => 1,
                'is_migration_agent' => 0,
                'office_id' => 1,
            ],
        ]);

        DB::table('branches')->insert([
            'id' => 1,
            'office_name' => 'Melbourne',
        ]);

        DB::table('matters')->insert([
            'id' => 1,
            'title' => 'General',
            'status' => 1,
            'is_for_company' => false,
        ]);

        $payload = ClientDetailChecklistsTab::build((object) [
            'id' => 1,
            'client_id' => 'C001',
            'is_company' => 0,
        ]);

        Assert::assertNull($payload['checklistCurrentMatterId']);
        Assert::assertFalse($payload['checklistCurrentMatterNeedsCostAssignment']);
        Assert::assertNotEmpty($payload['checklistMigrationAgents']);
        Assert::assertNotEmpty($payload['checklistPersonsResponsible']);
        Assert::assertNotEmpty($payload['checklistPersonsAssisting']);
        Assert::assertNotEmpty($payload['checklistOffices']);
        Assert::assertNotEmpty($payload['checklistMatterList']);
        Assert::assertEmpty($payload['checklistForms']);
        Assert::assertSame('Melbourne', $payload['checklistOffices']->first()->office_name);
    }

    #[Test]
    public function build_flags_matter_needing_cost_assignment_when_form_missing(): void
    {
        $this->createChecklistsTabSchema();

        DB::table('client_matters')->insert([
            'id' => 5,
            'client_id' => 1,
            'client_unique_matter_no' => 'APC_5',
            'matter_status' => 1,
        ]);

        $payload = ClientDetailChecklistsTab::build((object) [
            'id' => 1,
            'client_id' => 'C001',
            'is_company' => 0,
        ], 'APC_5');

        Assert::assertSame(5, $payload['checklistCurrentMatterId']);
        Assert::assertSame('APC_5', $payload['checklistCurrentMatterRef']);
        Assert::assertTrue($payload['checklistCurrentMatterNeedsCostAssignment']);
    }

    #[Test]
    public function attach_agreement_documents_binds_latest_active_and_ignores_archived_rows(): void
    {
        $this->createChecklistsTabSchema();

        DB::table('documents')->insert([
            [
                'id' => 100,
                'client_id' => 2,
                'client_matter_id' => 20,
                'doc_type' => 'agreement',
                'status' => 'signed',
                'signed_doc_link' => 'https://example.test/signed.pdf',
                'signature_doc_link' => null,
                'not_used_doc' => 1,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'id' => 101,
                'client_id' => 2,
                'client_matter_id' => 20,
                'doc_type' => 'agreement',
                'status' => 'signed',
                'signed_doc_link' => 'https://example.test/active-signed.pdf',
                'signature_doc_link' => null,
                'not_used_doc' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 102,
                'client_id' => 2,
                'client_matter_id' => 20,
                'doc_type' => 'agreement',
                'status' => 'sent',
                'signed_doc_link' => null,
                'signature_doc_link' => '[{"url":"https://example.test/sign"}]',
                'not_used_doc' => 1,
                'created_at' => now()->addMinute(),
                'updated_at' => now()->addMinute(),
            ],
        ]);

        $form = new CostAssignmentForm;
        $form->forceFill([
            'id' => 30,
            'client_id' => 2,
            'client_matter_id' => 20,
        ]);
        $forms = collect([$form]);

        $method = new \ReflectionMethod(ClientDetailChecklistsTab::class, 'attachAgreementDocuments');
        $method->invoke(null, $forms);

        $agreementDoc = $form->agreement_document;
        Assert::assertNotNull($agreementDoc);
        Assert::assertSame(101, (int) $agreementDoc->id);
        Assert::assertSame('signed', $agreementDoc->status);
    }

    private function createChecklistsTabSchema(): void
    {
        $schema = $this->app->make('db')->connection()->getSchemaBuilder();

        if (! $schema->hasTable('staff')) {
            $schema->create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->unsignedInteger('role')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->unsignedTinyInteger('is_migration_agent')->nullable();
                $table->unsignedBigInteger('office_id')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('branches')) {
            $schema->create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('office_name')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('matters')) {
            $schema->create('matters', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->unsignedTinyInteger('status')->nullable();
                $table->boolean('is_for_company')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('client_matters')) {
            $schema->create('client_matters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('client_unique_matter_no')->nullable();
                $table->unsignedInteger('matter_status')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('cost_assignment_forms')) {
            $schema->create('cost_assignment_forms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->unsignedBigInteger('agent_id')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('documents')) {
            $schema->create('documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('status')->nullable();
                $table->text('signed_doc_link')->nullable();
                $table->text('signature_doc_link')->nullable();
                $table->unsignedTinyInteger('not_used_doc')->nullable();
                $table->string('myfile')->nullable();
                $table->timestamps();
            });
        } else {
            foreach ([
                'client_id' => fn (Blueprint $table) => $table->unsignedBigInteger('client_id')->nullable(),
                'status' => fn (Blueprint $table) => $table->string('status')->nullable(),
                'signed_doc_link' => fn (Blueprint $table) => $table->text('signed_doc_link')->nullable(),
                'signature_doc_link' => fn (Blueprint $table) => $table->text('signature_doc_link')->nullable(),
                'not_used_doc' => fn (Blueprint $table) => $table->unsignedTinyInteger('not_used_doc')->nullable(),
            ] as $column => $definition) {
                if (! $schema->hasColumn('documents', $column)) {
                    $schema->table('documents', function (Blueprint $table) use ($definition) {
                        $definition($table);
                    });
                }
            }
        }
    }
}
