<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Tests\Feature;

use Illuminate\Auth\GenericUser;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use jeremykenedy\LaravelEmailDatabaseLog\Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        $app['config']->set('laravel-email-database-log-ui.enabled', true);
        $app['config']->set('laravel-email-database-log.per_page', 2);
    }

    protected function authorizeDashboard(): void
    {
        $this->actingAs(new GenericUser(['id' => 1]));
        Gate::define('viewEmailLog', fn ($user) => $user->id === 1);
    }

    protected function record(array $values = []): int
    {
        return DB::table('email_log')->insertGetId(array_merge([
            'date'    => '2026-09-17 12:00:00',
            'from'    => 'sender@example.com',
            'to'      => 'reader@example.com',
            'subject' => 'Order confirmation',
            'body'    => '<h1>Thank you</h1>',
        ], $values));
    }

    public function test_guests_cannot_read_emails(): void
    {
        $this->getJson('/email-log')->assertUnauthorized();
    }

    public function test_signed_in_users_are_denied_without_a_gate(): void
    {
        $this->actingAs(new GenericUser(['id' => 1]));
        $this->get('/email-log')->assertForbidden();
        $this->get('/email-log/1')->assertForbidden();
    }

    public function test_authorization_is_required_even_without_auth_middleware(): void
    {
        $this->withoutMiddleware(Authenticate::class);
        Gate::define('viewEmailLog', fn () => true);
        $this->get('/email-log')->assertForbidden();
    }

    public function test_gate_denials_apply_to_list_and_details(): void
    {
        $this->actingAs(new GenericUser(['id' => 2]));
        Gate::define('viewEmailLog', fn ($user) => $user->id === 1);
        $this->get('/email-log')->assertForbidden();
        $this->get('/email-log/1')->assertForbidden();
    }

    public function test_all_frameworks_render_and_empty_state_is_helpful(): void
    {
        $this->authorizeDashboard();
        foreach (['standalone' => 'el-shell', 'bootstrap5' => 'btn btn-primary', 'tailwind' => 'rounded-lg px-4'] as $framework => $class) {
            config(['laravel-email-database-log-ui.framework' => $framework]);
            $this->get('/email-log')->assertOk()->assertSee('No emails recorded yet')->assertSee($class);
        }
    }

    public function test_pagination_is_newest_first_and_excludes_large_columns(): void
    {
        $this->authorizeDashboard();
        $this->record(['subject' => 'Oldest']);
        $this->record(['subject' => 'Middle']);
        $this->record(['subject' => 'Newest', 'body' => 'Private body marker']);
        $response = $this->get('/email-log');
        $response->assertOk()->assertSeeInOrder(['Newest', 'Middle'])->assertDontSee('Oldest')->assertDontSee('Private body marker');
        $this->assertFalse(property_exists($response->viewData('logs')->first(), 'body'));
        $this->get('/email-log?page=2')->assertOk()->assertSee('Oldest')->assertDontSee('Newest');
    }

    public function test_search_covers_subject_sender_and_recipient_and_persists_on_next_page(): void
    {
        $this->authorizeDashboard();
        $this->record(['subject' => 'Find this']);
        $this->record(['from' => 'find@example.com']);
        $this->record(['to' => 'find@example.com']);
        $this->record(['subject' => 'Unrelated']);
        $this->get('/email-log?q=find')->assertOk()->assertSee('q=find')->assertDontSee('Unrelated');
        $this->get('/email-log?q=find&page=2')->assertOk()->assertSee('Find this');
    }

    public function test_search_rejects_arrays_and_oversized_input(): void
    {
        $this->authorizeDashboard();
        $this->getJson('/email-log?q[]=bad')->assertStatus(422);
        $this->getJson('/email-log?q='.str_repeat('a', 201))->assertStatus(422);
        $this->get('/email-log?q=missing')->assertOk()->assertSee('No matching emails');
    }

    public function test_detail_escapes_every_untrusted_field(): void
    {
        $this->authorizeDashboard();
        $payload = '<script>alert("unsafe")</script>';
        $id = $this->record(array_fill_keys(['from', 'to', 'cc', 'bcc', 'subject', 'body', 'headers', 'attachments'], $payload));
        $this->get('/email-log/'.$id)->assertOk()->assertDontSee($payload, false)->assertSee($payload)->assertSee('Message source');
        $this->get('/email-log')->assertOk()->assertDontSee($payload, false)->assertSee($payload);
    }

    public function test_missing_and_invalid_ids_return_not_found(): void
    {
        $this->authorizeDashboard();
        $this->get('/email-log/999')->assertNotFound();
        $this->get('/email-log/not-a-number')->assertNotFound();
    }

    public function test_disabling_dashboard_also_blocks_previously_registered_routes(): void
    {
        $this->authorizeDashboard();
        config(['laravel-email-database-log-ui.enabled' => false]);
        $this->get('/email-log')->assertNotFound();
    }

    public function test_details_handle_empty_optional_fields(): void
    {
        $this->authorizeDashboard();
        $id = $this->record(['subject' => '', 'from' => null, 'to' => null, 'body' => '']);
        $this->get('/email-log/'.$id)->assertOk()->assertSee('(No subject)')->assertSee('No attachments recorded.')->assertSee('No headers recorded.');
    }

    public function test_dashboard_has_no_write_routes(): void
    {
        $this->authorizeDashboard();
        $id = $this->record();
        $this->post('/email-log')->assertStatus(405);
        $this->delete('/email-log/'.$id)->assertStatus(405);
        $this->assertSame(1, DB::table('email_log')->count());
    }

    public function test_unknown_framework_and_missing_optional_package_have_safe_fallbacks(): void
    {
        $this->authorizeDashboard();
        config(['laravel-email-database-log-ui.framework' => 'unknown', 'laravel-email-database-log-ui.ui_kit' => true]);
        $this->get('/email-log')->assertOk();
    }

    public function test_private_mail_responses_are_not_cacheable(): void
    {
        $this->authorizeDashboard();
        $id = $this->record();
        foreach (['/email-log', '/email-log/'.$id] as $path) {
            $response = $this->get($path)->assertOk()->assertHeader('Referrer-Policy', 'no-referrer');
            $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        }
    }

    public function test_installed_ui_kit_renders_its_real_blade_component(): void
    {
        if (! class_exists('Jeremykenedy\\LaravelUiKit\\Providers\\UiKitServiceProvider')) {
            $this->markTestSkipped('Optional UI Kit integration is covered in its own CI job.');
        }
        $this->authorizeDashboard();
        config(['laravel-email-database-log-ui.ui_kit' => true]);
        $this->get('/email-log')->assertOk()->assertSee('A log entry does not confirm delivery.');
    }
}
