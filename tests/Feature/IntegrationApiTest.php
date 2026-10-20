<?php

namespace Tests\Feature;

use App\Models\{Page, Question, QuestionUserResponse, User};
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\{DB, Schema};

class IntegrationApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'cache.default' => 'array', 'integration.a11_token' => str_repeat('x', 48)]);
        DB::purge('sqlite');
        DB::unprepared(<<<'SQL'
CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, password TEXT, role TEXT, tipo_usuario TEXT, area TEXT, puesto TEXT, employee_code TEXT, created_at TEXT, updated_at TEXT, deleted_at TEXT);
CREATE TABLE pages (id INTEGER PRIMARY KEY, title TEXT, slug TEXT, destinado_a TEXT, "order" INTEGER, created_at TEXT, updated_at TEXT);
CREATE TABLE questions (id INTEGER PRIMARY KEY, page_id INTEGER, question_text TEXT, question_type TEXT, is_active INTEGER, "order" INTEGER, created_at TEXT, updated_at TEXT);
CREATE TABLE question_user_responses (id INTEGER PRIMARY KEY, question_id INTEGER, user_id INTEGER, answer TEXT, is_correct INTEGER, created_at TEXT, updated_at TEXT);
CREATE TABLE induction_page_user (page_id INTEGER, user_id INTEGER);
SQL);
        User::forceCreate(['id' => 52, 'name' => 'Colaborador', 'employee_code' => 'ID1801', 'email' => 'test@example.test',
            'password' => 'not-exposed', 'role' => 'user', 'tipo_usuario' => 'Colaborador']);
        Page::forceCreate(['id' => 1, 'title' => 'Seguridad', 'slug' => 'seguridad', 'destinado_a' => ['Colaborador'], 'order' => 1]);
        Page::forceCreate(['id' => 2, 'title' => 'Privado para estadía', 'slug' => 'otro', 'destinado_a' => ['Estadia'], 'order' => 2]);
        foreach ([1 => 'opcion_multiple', 2 => 'abierta'] as $id => $type) {
            Question::forceCreate(['id' => $id, 'page_id' => 1, 'question_text' => 'Pregunta '.$id, 'question_type' => $type, 'is_active' => 1, 'order' => $id]);
            QuestionUserResponse::create(['user_id' => 52, 'question_id' => $id, 'answer' => 'Respuesta '.$id, 'is_correct' => $id === 1 ? 0 : null]);
        }
    }

    private function endpoint(string $code = 'ID1801'): string
    {
        return '/api/v1/induccion?'.http_build_query(['employee_code' => $code]);
    }

    public function test_completion_event_preserves_offset_and_checks_acceptance(): void
    {
        config(['d06_events.a11_events_url' => 'https://a11.test/api/events', 'd06_events.token' => str_repeat('e', 48)]);
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake(['a11.test/*' => \Illuminate\Support\Facades\Http::sequence()->push(['accepted' => true])->push([], 401)]);
        app(\App\Services\A11EventNotifier::class)->moduleCompleted(User::find(52), Page::find(1),
            ['revision' => str_repeat('a', 64), 'completed_at' => '2026-10-20T13:00:00-06:00']);
        \Illuminate\Support\Facades\Http::assertSent(fn ($request) =>
            $request['completed_at'] === '2026-10-20T13:00:00-06:00' && $request['employee_code'] === 'ID1801');
        \Illuminate\Support\Facades\Log::shouldReceive('warning')->once()->with('A11 no aceptó el evento de D06.',
            ['employee_code' => 'ID1801', 'status' => 401]);
        app(\App\Services\A11EventNotifier::class)->moduleCompleted(User::find(52), Page::find(1), ['revision' => str_repeat('a', 64)]);
    }

    public function test_api_requires_its_own_token_even_with_admin_session(): void
    {
        $this->getJson($this->endpoint())->assertUnauthorized();
        $this->actingAs(User::find(52))->getJson($this->endpoint())->assertUnauthorized();
        $this->withToken('wrong')->getJson($this->endpoint())->assertUnauthorized();
        config(['integration.a11_token' => null]);
        $this->withToken(str_repeat('x', 48))->getJson($this->endpoint())->assertUnauthorized();
    }

    public function test_api_separates_completion_accuracy_and_changes_revision(): void
    {
        $response = $this->withToken(str_repeat('x', 48))->getJson($this->endpoint())->assertOk()
            ->assertJsonPath('summary.percentage', 100)->assertJsonPath('summary.accuracy', 0)
            ->assertJsonPath('modules.0.answered', 2)->assertJsonPath('modules.0.scored', 1)
            ->assertJsonPath('modules.0.questions.1.correct', null)->assertJsonCount(1, 'modules')
            ->assertDontSee('not-exposed')->assertDontSee('test@example.test')->assertDontSee('Privado para estadía');
        $revision = $response->json('modules.0.revision');
        $this->getJson($this->endpoint())->assertJsonPath('modules.0.revision', $revision);
        QuestionUserResponse::where('question_id', 1)->update(['answer' => 'Corregida', 'is_correct' => 1]);
        $next = $this->getJson($this->endpoint())->assertJsonPath('summary.accuracy', 100);
        $this->assertNotSame($revision, $next->json('modules.0.revision'));
        $this->assertStringContainsString('no-store', $next->headers->get('Cache-Control'));
    }

    public function test_api_rejects_missing_duplicate_and_deleted_codes(): void
    {
        $this->withToken(str_repeat('x', 48))->getJson($this->endpoint('OTRO'))->assertNotFound();
        $this->getJson($this->endpoint('id1801'))->assertNotFound();
        $this->getJson('/api/v1/induccion')->assertUnprocessable();
        User::forceCreate(['id' => 53, 'name' => 'Duplicado', 'employee_code' => 'ID1801']);
        $this->getJson($this->endpoint())->assertStatus(409);
        User::whereIn('id', [52, 53])->delete();
        $this->getJson($this->endpoint())->assertNotFound();
    }

    public function test_employee_code_migration_preserves_existing_codes_and_is_idempotent(): void
    {
        $migration = require database_path('migrations/2026_10_19_000001_add_employee_code_for_integration.php');
        $migration->up();
        $migration->up();
        $this->assertTrue(Schema::hasIndex('users', ['employee_code'], 'unique'));
        $this->assertSame('ID1801', User::find(52)->employee_code);
    }
}
