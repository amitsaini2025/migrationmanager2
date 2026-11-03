<?php

namespace Tests\Feature;

use App\Http\Controllers\CRM\SignatureDashboardController;
use App\Models\Admin;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SignatureAttachEntitiesSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
    }

    #[Test]
    public function attach_entities_route_is_registered(): void
    {
        $this->assertTrue(Route::has('signatures.attach-entities'));
        $this->assertSame(url('/signatures/attach-entities'), route('signatures.attach-entities'));
    }

    #[Test]
    public function index_and_show_views_do_not_embed_full_client_lead_json(): void
    {
        $dashboard = file_get_contents(resource_path('views/crm/signatures/dashboard.blade.php'));
        $show = file_get_contents(resource_path('views/crm/signatures/show.blade.php'));

        $this->assertNotFalse($dashboard);
        $this->assertNotFalse($show);
        $this->assertStringNotContainsString('@json($clients', $dashboard);
        $this->assertStringNotContainsString('@json($leads', $dashboard);
        $this->assertStringNotContainsString('@json($clients', $show);
        $this->assertStringNotContainsString('@json($leads', $show);
        $this->assertStringContainsString('signatures.attach-entities', $dashboard);
        $this->assertStringContainsString('signatures.attach-entities', $show);
    }

    #[Test]
    public function search_returns_empty_when_query_too_short(): void
    {
        Admin::factory()->create([
            'type' => 'client',
            'first_name' => 'Alice',
            'last_name' => 'Client',
            'email' => 'alice-client@example.com',
            'is_deleted' => null,
        ]);

        $response = $this->search(['type' => 'client', 'q' => 'A']);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'results' => [],
        ]);
    }

    #[Test]
    public function search_finds_clients_by_name_with_limit(): void
    {
        $match = Admin::factory()->create([
            'type' => 'client',
            'first_name' => 'Zelda',
            'last_name' => 'AttachClient',
            'email' => 'zelda.attach@example.com',
            'is_deleted' => null,
        ]);

        Admin::factory()->create([
            'type' => 'client',
            'first_name' => 'Other',
            'last_name' => 'Person',
            'email' => 'other.attach@example.com',
            'is_deleted' => null,
        ]);

        $response = $this->search(['type' => 'client', 'q' => 'Zelda', 'limit' => 10]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'results');
        $response->assertJsonPath('results.0.id', $match->id);
        $response->assertJsonPath('results.0.email', 'zelda.attach@example.com');
    }

    #[Test]
    public function search_finds_leads_by_exact_email(): void
    {
        $lead = Admin::factory()->create([
            'type' => 'lead',
            'first_name' => 'Jamie',
            'last_name' => 'Lead',
            'email' => 'jamie.lead@example.com',
            'is_deleted' => null,
            'is_archived' => 0,
        ]);

        $response = $this->search([
            'type' => 'lead',
            'email' => 'jamie.lead@example.com',
        ]);

        $response->assertOk();
        $response->assertJsonCount(1, 'results');
        $response->assertJsonPath('results.0.id', $lead->id);
    }

    #[Test]
    public function search_excludes_deleted_clients(): void
    {
        Admin::factory()->create([
            'type' => 'client',
            'first_name' => 'Gone',
            'last_name' => 'Client',
            'email' => 'gone.client@example.com',
            'is_deleted' => 1,
        ]);

        $response = $this->search(['type' => 'client', 'q' => 'Gone']);

        $response->assertOk();
        $response->assertJsonPath('results', []);
    }

    private function search(array $params)
    {
        $request = Request::create('/signatures/attach-entities', 'GET', $params);
        $controller = app(SignatureDashboardController::class);

        return $this->createTestResponse(
            $controller->searchAttachEntities($request),
            $request
        );
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('password')->nullable();
                $table->string('type')->nullable();
                $table->string('country')->nullable();
                $table->string('state')->nullable();
                $table->string('city')->nullable();
                $table->string('address')->nullable();
                $table->string('zip')->nullable();
                $table->integer('status')->nullable();
                $table->date('dob')->nullable();
                $table->unsignedInteger('is_deleted')->nullable();
                $table->unsignedInteger('is_archived')->nullable();
                $table->unsignedTinyInteger('is_company')->nullable();
                $table->string('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('lead_status')->nullable();
                $table->string('source')->nullable();
                $table->string('remember_token')->nullable();
                $table->timestamps();
            });
        }
    }
}
